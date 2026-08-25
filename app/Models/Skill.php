<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = ['skill_group_id', 'name', 'sort_order'];

    public function skillGroup(): BelongsTo
    {
        return $this->belongsTo(SkillGroup::class);
    }
}
