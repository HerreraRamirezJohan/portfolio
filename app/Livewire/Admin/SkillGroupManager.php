<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Livewire\Admin\Concerns\ReordersRecords;
use App\Models\SkillGroup;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SkillGroupManager extends Component
{
    use HasLocaleTabs, ReordersRecords;

    public ?int $editingId = null;

    /** @var array<string, string> */
    public array $label = [];

    /** @var array<string, string> */
    public array $description = [];

    /**
     * Skill names are not translated -- they are proper nouns (PHP, React).
     *
     * @var array<int, string>
     */
    public array $skillNames = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();
        $this->resetForm();
    }

    /** @return array<string, string> */
    protected function rules(): array
    {
        return [
            'label.*' => 'nullable|string|max:255',
            'description.*' => 'nullable|string|max:2000',
            'skillNames.*' => 'nullable|string|max:100',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    public function edit(int $id): void
    {
        $row = $this->records()->firstWhere('id', $id);

        if (! $row) {
            return;
        }

        $this->editingId = $row->id;
        $this->label = $this->fillTranslations($row->getTranslations('label'));
        $this->description = $this->fillTranslations($row->getTranslations('description'));
        $this->skillNames = $row->skills->pluck('name')->all();
    }

    public function addSkill(): void
    {
        $this->skillNames[] = '';
    }

    public function removeSkill(int $index): void
    {
        unset($this->skillNames[$index]);
        $this->skillNames = array_values($this->skillNames);
    }

    public function save(): void
    {
        $this->validate();

        $attributes = [
            'label' => array_filter($this->label),
            'description' => array_filter($this->description),
        ];

        if ($this->editingId) {
            $group = SkillGroup::whereKey($this->editingId)->firstOrFail();
            $group->update($attributes);
        } else {
            $user = User::firstOrFail();
            $attributes['user_id'] = $user->id;
            $attributes['sort_order'] = (int) $user->skillGroups()->max('sort_order') + 1;
            $group = SkillGroup::create($attributes);
        }

        // Replace the skill list wholesale -- simplest correct behaviour for a
        // short, hand-edited list, and keeps sort_order contiguous.
        $names = collect($this->skillNames)->map(fn ($n) => trim((string) $n))->filter()->values();

        $group->skills()->delete();

        foreach ($names as $index => $name) {
            $group->skills()->create(['name' => $name, 'sort_order' => $index + 1]);
        }

        $this->resetForm();
        session()->flash('status', __('Saved.'));
    }

    public function delete(int $id): void
    {
        SkillGroup::whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        session()->flash('status', __('Deleted.'));
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->label = $this->emptyTranslations();
        $this->description = $this->emptyTranslations();
        $this->skillNames = [];
        $this->resetValidation();
    }

    /** @return Collection<int, SkillGroup> */
    public function records(): Collection
    {
        return User::firstOrFail()->skillGroups()->with('skills')->ordered()->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.skill-group-manager', [
            'rows' => $this->records(),
        ]);
    }
}
