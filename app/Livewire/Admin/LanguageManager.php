<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Livewire\Admin\Concerns\ReordersRecords;
use App\Models\Language;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class LanguageManager extends Component
{
    use HasLocaleTabs, ReordersRecords;

    public ?int $editingId = null;

    public string $code = '';

    /** @var array<string, string> */
    public array $name = [];

    /** @var array<string, string> */
    public array $level_label = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();
        $this->resetForm();
    }

    /** @return array<string, string> */
    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:5',
            'name.*' => 'nullable|string|max:255',
            'level_label.*' => 'nullable|string|max:255',
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
        $this->code = $row->code;
        $this->name = $this->fillTranslations($row->getTranslations('name'));
        $this->level_label = $this->fillTranslations($row->getTranslations('level_label'));
    }

    public function save(): void
    {
        $this->validate();

        $attributes = [
            'code' => $this->code,
            'name' => array_filter($this->name),
            'level_label' => array_filter($this->level_label),
        ];

        if ($this->editingId) {
            Language::whereKey($this->editingId)->firstOrFail()->update($attributes);
        } else {
            $user = User::firstOrFail();
            $attributes['user_id'] = $user->id;
            $attributes['sort_order'] = (int) $user->languages()->max('sort_order') + 1;
            Language::create($attributes);
        }

        $this->resetForm();
        session()->flash('status', __('Saved.'));
    }

    public function delete(int $id): void
    {
        Language::whereKey($id)->delete();

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
        $this->code = '';
        $this->name = $this->emptyTranslations();
        $this->level_label = $this->emptyTranslations();
        $this->resetValidation();
    }

    /** @return Collection<int, Language> */
    public function records(): Collection
    {
        return User::firstOrFail()->languages()->ordered()->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.language-manager', [
            'rows' => $this->records(),
        ]);
    }
}
