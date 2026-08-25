<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Livewire\Admin\Concerns\ReordersRecords;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ExperienceManager extends Component
{
    use HasLocaleTabs, ReordersRecords;

    public ?int $editingId = null;

    public string $company = '';

    public string $start_date = '';

    public string $end_date = '';

    public bool $is_current = false;

    /** @var array<string, string> */
    public array $role = [];

    /** @var array<string, string> */
    public array $location = [];

    /** @var array<string, array<int, array{label: string, body: string}>> */
    public array $bullets = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();
        $this->resetForm();
    }

    /** @return array<string, string> */
    protected function rules(): array
    {
        return [
            'company' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'role.*' => 'nullable|string|max:255',
            'location.*' => 'nullable|string|max:255',
            'bullets.*.*.label' => 'nullable|string|max:255',
            'bullets.*.*.body' => 'nullable|string|max:2000',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    public function edit(int $id): void
    {
        $job = $this->records()->firstWhere('id', $id);

        if (! $job) {
            return;
        }

        $this->editingId = $job->id;
        $this->company = $job->company;
        $this->start_date = $job->start_date->toDateString();
        $this->end_date = $job->end_date?->toDateString() ?? '';
        $this->is_current = $job->is_current;
        $this->role = $this->fillTranslations($job->getTranslations('role'));
        $this->location = $this->fillTranslations($job->getTranslations('location'));
        $this->bullets = $this->fillTranslations($job->getTranslations('bullets'), []);
    }

    public function addBullet(): void
    {
        $this->bullets[$this->formLocale][] = ['label' => '', 'body' => ''];
    }

    public function removeBullet(int $index): void
    {
        unset($this->bullets[$this->formLocale][$index]);
        $this->bullets[$this->formLocale] = array_values($this->bullets[$this->formLocale]);
    }

    public function save(): void
    {
        $this->validate();

        $attributes = [
            'company' => $this->company,
            'start_date' => $this->start_date,
            'end_date' => $this->is_current ? null : ($this->end_date ?: null),
            'is_current' => $this->is_current,
            'role' => array_filter($this->role),
            'location' => array_filter($this->location),
            'bullets' => $this->cleanBullets(),
        ];

        if ($this->editingId) {
            WorkExperience::whereKey($this->editingId)->firstOrFail()->update($attributes);
        } else {
            $user = User::firstOrFail();
            $attributes['user_id'] = $user->id;
            $attributes['sort_order'] = (int) $user->workExperiences()->max('sort_order') + 1;
            WorkExperience::create($attributes);
        }

        $this->resetForm();
        session()->flash('status', __('Saved.'));
    }

    public function delete(int $id): void
    {
        WorkExperience::whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        session()->flash('status', __('Deleted.'));
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    /**
     * Drop bullets with no body, and the label key when it is blank, so the
     * public view's `filled($bullet['label'])` check stays meaningful.
     *
     * @return array<string, array<int, array{label: string|null, body: string}>>
     */
    private function cleanBullets(): array
    {
        $result = [];

        foreach ($this->bullets as $locale => $rows) {
            $cleaned = collect($rows)
                ->filter(fn (array $row) => filled($row['body'] ?? null))
                ->map(fn (array $row) => [
                    'label' => filled($row['label'] ?? null) ? $row['label'] : null,
                    'body' => $row['body'],
                ])
                ->values()
                ->all();

            if ($cleaned !== []) {
                $result[$locale] = $cleaned;
            }
        }

        return $result;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->company = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_current = false;
        $this->role = $this->emptyTranslations();
        $this->location = $this->emptyTranslations();
        $this->bullets = array_fill_keys($this->locales, []);
        $this->resetValidation();
    }

    /** @return Collection<int, WorkExperience> */
    public function records(): Collection
    {
        return User::firstOrFail()->workExperiences()->ordered()->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.experience-manager', [
            'jobs' => $this->records(),
        ]);
    }
}
