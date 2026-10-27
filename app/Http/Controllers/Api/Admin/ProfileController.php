<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfilePhotoRequest;
use App\Http\Requests\Admin\ProfileRequest;
use App\Http\Resources\Admin\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use App\Support\Translations;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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

    public function updatePhoto(ProfilePhotoRequest $request): ProfileResource
    {
        $user = User::firstOrFail();
        $profile = $user->profile()->firstOrNew();
        $previous = $profile->photo_path;

        $profile->photo_path = $request->file('photo')->store('profile', 'public');
        $profile->save();

        $this->deletePhotoFile($previous);

        return new ProfileResource($user->load('profile'));
    }

    public function destroyPhoto(): Response
    {
        $profile = User::firstOrFail()->profile;

        if ($profile?->photo_path) {
            $this->deletePhotoFile($profile->photo_path);
            $profile->update(['photo_path' => null]);
        }

        return response()->noContent();
    }

    /**
     * Remove an uploaded photo from disk, but never the bundled default.
     */
    private function deletePhotoFile(?string $path): void
    {
        if ($path && $path !== Profile::DEFAULT_PHOTO) {
            Storage::disk('public')->delete($path);
        }
    }
}
