<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleMenuPermission extends Model
{
    protected $fillable = ['menu_id', 'can_view', 'can_create', 'can_update', 'can_delete'];

    protected function casts(): array
    {
        return ['can_view' => 'boolean', 'can_create' => 'boolean', 'can_update' => 'boolean', 'can_delete' => 'boolean'];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
