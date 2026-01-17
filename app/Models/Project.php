<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'hourly_rate',
        'description',
    ];

    public function user() :belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeEntries() :hasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
}
