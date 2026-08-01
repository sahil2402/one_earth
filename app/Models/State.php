<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class State extends Model
{
    protected $fillable = [
        'country_id', 'state_type', 'name', 'slug', 'image_path',
        'our_operation', 'is_capital', 'lat_log_name', 'address',
        'latitude', 'longitude', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'our_operation' => 'boolean',
        'is_capital' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
