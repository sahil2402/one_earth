<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'country_id', 'state_id', 'name', 'slug', 'time_to_visit',
        'currency', 'language', 'introduction', 'lat_log_name',
        'address', 'latitude', 'longitude', 'description', 'seo_title',
        'meta_keyword', 'meta_description', 'banner_image', 'thumb_image',
        'our_operation', 'is_capital', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'our_operation' => 'boolean',
        'is_capital' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
