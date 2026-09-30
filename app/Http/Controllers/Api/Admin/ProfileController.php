<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileRequest;
use App\Http\Resources\Admin\ProfileResource;
use App\Models\User;
use App\Support\Translations;

class ProfileController extends Controller
{
    public function show(): ProfileResource
    {
        return new ProfileResource(User::with('profile')->firstOrFail());
    }

    public function update(ProfileRequest $request): ProfileResource
    {
        $user = User::firstOrFail();
        $user->update(['name' => $request->input('name')]);

        Translations::update($user->profile()->firstOrNew(), $request->profileAttributes());

        return new ProfileResource($user->load('profile'));
    }
}
