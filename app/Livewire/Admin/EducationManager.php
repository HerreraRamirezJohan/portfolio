<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Livewire\Admin\Concerns\ReordersRecords;
use App\Models\Education;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EducationManager extends Component
{
    use HasLocaleTabs, ReordersRecords;

    public ?int $editingId = null;

    public string $institution = '';

    public ?int $start_year = null;

    public ?int $end_year = null;

    /** @var array<string, string> */
    public array $degree = [];

    /** @var array<string, string> */
    public array $location = [];

    /** @var array<string, string> */
    public array $notes = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();
        $this->resetForm();
    }

    /** @return array<string, string> */
    protected function rules(): array
    {
        return [
            'institution' => 'required|string|max:255',
            'start_year' => 'nullable|integer|min:1900|max:2100',
            'end_year' => 'nullable|integer|min:1900|max:2100|gte:start_year',
            'degree.*' => 'nullable|string|max:255',
            'location.*' => 'nullable|string|max:255',
            'notes.*' => 'nullable|string|max:2000',
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
        $this->institution = $row->institution;
        $this->start_year = $row->start_year;
        $this->end_year = $row->end_year;
        $this->degree = $this->fillTranslations($row->getTranslations('degree'));
        $this->location = $this->fillTranslations($row->getTranslations('location'));
        $this->notes = $this->fillTranslations($row->getTranslations('notes'));
    }

    public function save(): void
    {
        $this->validate();

        $attributes = [
            'institution' => $this->institution,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'degree' => array_filter($this->degree),
            'location' => array_filter($this->location),
            'notes' => array_filter($this->notes),
        ];

        if ($this->editingId) {
            Education::whereKey($this->editingId)->firstOrFail()->update($attributes);
        } else {
            $user = User::firstOrFail();
            $attributes['user_id'] = $user->id;
            $attributes['sort_order'] = (int) $user->educations()->max('sort_order') + 1;
            Education::create($attributes);
        }

        $this->resetForm();
        session()->flash('status', __('Saved.'));
    }

    public function delete(int $id): void
    {
        Education::whereKey($id)->delete();

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
        $this->institution = '';
        $this->start_year = null;
        $this->end_year = null;
        $this->degree = $this->emptyTranslations();
        $this->location = $this->emptyTranslations();
        $this->notes = $this->emptyTranslations();
        $this->resetValidation();
    }

    /** @return Collection<int, Education> */
    public function records(): Collection
    {
        return User::firstOrFail()->educations()->ordered()->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.education-manager', [
            'rows' => $this->records(),
        ]);
    }
}
