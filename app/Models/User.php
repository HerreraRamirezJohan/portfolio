<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Contact details are 1:1 -- one phone, one LinkedIn per person.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderBy('sort_order');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class)->orderBy('sort_order');
    }

    public function skillGroups(): HasMany
    {
        return $this->hasMany(SkillGroup::class)->orderBy('sort_order');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class)->orderBy('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('sort_order');
    }
}
