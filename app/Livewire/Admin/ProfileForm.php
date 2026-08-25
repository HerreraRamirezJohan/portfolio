<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\HasLocaleTabs;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfileForm extends Component
{
    use HasLocaleTabs;

    public string $name = '';

    public string $phone = '';

    public string $public_email = '';

    public string $linkedin_url = '';

    public string $github_url = '';

    public string $website_url = '';

    /** @var array<string, string> */
    public array $headline = [];

    /** @var array<string, string> */
    public array $location = [];

    /** @var array<string, string> */
    public array $summary = [];

    public function mount(): void
    {
        $this->mountHasLocaleTabs();

        $user = $this->user();
        $profile = $user->profile;

        $this->name = $user->name;
        $this->phone = $profile?->phone ?? '';
        $this->public_email = $profile?->public_email ?? '';
        $this->linkedin_url = $profile?->linkedin_url ?? '';
        $this->github_url = $profile?->github_url ?? '';
        $this->website_url = $profile?->website_url ?? '';

        $this->headline = $this->fillTranslations($profile?->getTranslations('headline') ?? []);
        $this->location = $this->fillTranslations($profile?->getTranslations('location') ?? []);
        $this->summary = $this->fillTranslations($profile?->getTranslations('summary') ?? []);
    }

    /** @return array<string, string> */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'public_email' => 'nullable|email|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'headline.*' => 'nullable|string|max:255',
            'location.*' => 'nullable|string|max:255',
            'summary.*' => 'nullable|string|max:5000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $user = $this->user();
        $user->update(['name' => $this->name]);

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $this->phone ?: null,
                'public_email' => $this->public_email ?: null,
                'linkedin_url' => $this->linkedin_url ?: null,
                'github_url' => $this->github_url ?: null,
                'website_url' => $this->website_url ?: null,
                'headline' => array_filter($this->headline),
                'location' => array_filter($this->location),
                'summary' => array_filter($this->summary),
            ],
        );

        session()->flash('status', __('Saved.'));
    }

    private function user(): User
    {
        return User::firstOrFail();
    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.profile-form');
    }
}
