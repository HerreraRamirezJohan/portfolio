<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        $user = User::withCount([
            'workExperiences', 'educations', 'skillGroups', 'languages', 'projects',
        ])->firstOrFail();

        return view('livewire.admin.dashboard', ['user' => $user]);
    }
}
