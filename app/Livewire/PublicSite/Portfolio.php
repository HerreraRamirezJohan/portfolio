<?php

namespace App\Livewire\PublicSite;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Portfolio extends Component
{
    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $user = User::with([
            'profile',
            'workExperiences',
            'educations',
            'skillGroups.skills',
            'languages',
            'projects' => fn ($query) => $query->published(),
        ])->firstOrFail();

        return view('livewire.public-site.portfolio', [
            'user' => $user,
            'profile' => $user->profile,
        ])->title(trim($user->name.' — '.($user->profile?->headline ?? '')));
    }
}
