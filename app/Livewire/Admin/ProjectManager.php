<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Livewire\Admin\Concerns\ReordersRecords;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProjectManager extends Component
{
    use HasLocaleTabs, ReordersRecords;

    public ?int $editingId = null;

    public string $slug = '';

    public string $repo_url = '';

    public string $live_url = '';

    public bool $is_published = true;

    /** @var array<string, string> */
    public array $title = [];

    /** @var array<string, string> */
    public array $summary = [];

    /** @var array<string, string> */
    public array $description = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();
        $this->resetForm();
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                'unique:projects,slug'.($this->editingId ? ','.$this->editingId : ''),
            ],
            'repo_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'is_published' => 'boolean',
            'title.*' => 'nullable|string|max:255',
            'summary.*' => 'nullable|string|max:1000',
            'description.*' => 'nullable|string|max:5000',
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
        $this->slug = $row->slug;
        $this->repo_url = $row->repo_url ?? '';
        $this->live_url = $row->live_url ?? '';
        $this->is_published = $row->is_published;
        $this->title = $this->fillTranslations($row->getTranslations('title'));
        $this->summary = $this->fillTranslations($row->getTranslations('summary'));
        $this->description = $this->fillTranslations($row->getTranslations('description'));
    }

    /**
     * Suggest a slug from the title the first time one is typed.
     */
    public function updatedTitle(): void
    {
        if ($this->editingId === 0 && blank($this->slug)) {
            $this->slug = Str::slug($this->title[$this->formLocale] ?? '');
        }
    }

    public function save(): void
    {
        $this->validate();

        $attributes = [
            'slug' => $this->slug,
            'repo_url' => $this->repo_url ?: null,
            'live_url' => $this->live_url ?: null,
            'is_published' => $this->is_published,
            'title' => array_filter($this->title),
            'summary' => array_filter($this->summary),
            'description' => array_filter($this->description),
        ];

        if ($this->editingId) {
            Project::whereKey($this->editingId)->firstOrFail()->update($attributes);
        } else {
            $user = User::firstOrFail();
            $attributes['user_id'] = $user->id;
            $attributes['sort_order'] = (int) $user->projects()->max('sort_order') + 1;
            Project::create($attributes);
        }

        $this->resetForm();
        session()->flash('status', __('Saved.'));
    }

    public function delete(int $id): void
    {
        Project::whereKey($id)->delete();

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
        $this->slug = '';
        $this->repo_url = '';
        $this->live_url = '';
        $this->is_published = true;
        $this->title = $this->emptyTranslations();
        $this->summary = $this->emptyTranslations();
        $this->description = $this->emptyTranslations();
        $this->resetValidation();
    }

    /** @return Collection<int, Project> */
    public function records(): Collection
    {
        return User::firstOrFail()->projects()->ordered()->get();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.project-manager', [
            'rows' => $this->records(),
        ]);
    }
}
