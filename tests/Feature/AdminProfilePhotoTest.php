<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CvSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(CvSeeder::class);
        $this->actingAs(User::firstOrFail());
    }

    private function photoPath(): ?string
    {
        return User::firstOrFail()->profile->photo_path;
    }

    public function test_a_photo_can_be_uploaded(): void
    {
        $this->post('/api/admin/profile/photo', [
            'photo' => UploadedFile::fake()->image('me.jpg', 400, 400),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.photo_url', Storage::url($this->photoPath()));

        $this->assertStringStartsWith('profile/', $this->photoPath());
        Storage::disk('public')->assertExists($this->photoPath());
    }

    public function test_a_new_photo_replaces_the_old_file(): void
    {
        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('a.png')], ['Accept' => 'application/json'])->assertOk();
        $first = $this->photoPath();

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('b.png')], ['Accept' => 'application/json'])->assertOk();

        $this->assertNotSame($first, $this->photoPath());
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($this->photoPath());
    }

    public function test_only_images_up_to_two_megabytes_are_accepted(): void
    {
        $headers = ['Accept' => 'application/json'];

        $this->post('/api/admin/profile/photo', [], $headers)
            ->assertUnprocessable()->assertJsonValidationErrors('photo');

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')], $headers)
            ->assertUnprocessable()->assertJsonValidationErrors('photo');

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('huge.jpg')->size(3000)], $headers)
            ->assertUnprocessable()->assertJsonValidationErrors('photo');

        $this->assertNull($this->photoPath());
    }

    public function test_the_photo_can_be_removed(): void
    {
        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('a.webp')], ['Accept' => 'application/json'])->assertOk();
        $path = $this->photoPath();

        $this->deleteJson('/api/admin/profile/photo')->assertNoContent();

        $this->assertNull($this->photoPath());
        Storage::disk('public')->assertMissing($path);
    }

    public function test_the_public_site_shows_the_new_photo_immediately(): void
    {
        // Prime the cache with the photo-less payload.
        $this->getJson('/api/en/home')->assertJsonPath('data.person.photo_url', null);

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json'])->assertOk();

        $this->getJson('/api/en/home')
            ->assertJsonPath('data.person.photo_url', Storage::url($this->photoPath()));
    }

    public function test_reseeding_keeps_an_uploaded_photo(): void
    {
        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('mine.jpg')], ['Accept' => 'application/json'])->assertOk();
        $mine = $this->photoPath();

        // The bundled default exists on disk, but it must not win over an upload.
        Storage::disk('public')->put('profile/profile.webp', 'default');

        $this->seed(CvSeeder::class);

        $this->assertSame($mine, $this->photoPath());
    }

    public function test_the_bundled_default_photo_is_never_deleted(): void
    {
        // The default is not tracked in git, so replacing or removing it from
        // the admin must not destroy the only copy.
        Storage::disk('public')->put('profile/profile.webp', 'default');
        $this->seed(CvSeeder::class);
        $this->assertSame('profile/profile.webp', $this->photoPath());

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('new.jpg')], ['Accept' => 'application/json'])->assertOk();
        Storage::disk('public')->assertExists('profile/profile.webp');

        $this->deleteJson('/api/admin/profile/photo')->assertNoContent();
        Storage::disk('public')->assertExists('profile/profile.webp');

        $this->post('/api/admin/profile/photo', ['photo' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json'])->assertOk();
        $uploaded = $this->photoPath();

        $this->seed(CvSeeder::class);
        $this->assertSame($uploaded, $this->photoPath());
    }
}
