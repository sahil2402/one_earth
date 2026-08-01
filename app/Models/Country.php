<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name', 'slug', 'code', 'address', 'latitude', 'longitude',
        'banner_image', 'summary', 'description', 'iso_code',
        'phone_code', 'isd_code', 'is_active', 'created_by', 'updated_by'
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
