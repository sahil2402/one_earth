<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        Domain::firstOrCreate(
            ['domain_name' => 'example.com'],
            [
                'smtp_host' => 'smtp.example.com',
                'smtp_port' => '587',
                'smtp_user' => 'user@example.com',
                'smtp_password' => 'secret',
                'email_from' => 'noreply@example.com',
                'email_from_name' => 'Example Site',
                'email_to_admin_user' => 'admin@example.com',
                'email_header' => '<p>Example Header</p>',
                'email_footer' => '<p>Example Footer</p>',
                'logo_path' => null,
                'created_by' => 'system',
                'updated_by' => 'system',
            ]
        );
    }
}
