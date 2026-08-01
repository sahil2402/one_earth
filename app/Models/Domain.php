<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_password',
        'email_from',
        'email_from_name',
        'email_to_admin_user',
        'email_header',
        'email_footer',
        'logo_path',
        'created_by',
        'updated_by',
    ];
}
