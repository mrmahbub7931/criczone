<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',  'display_name' => 'Administrator', 'description' => 'Full access to all dashboard features'],
            ['name' => 'editor', 'display_name' => 'Editor',        'description' => 'Can create, edit, and publish articles'],
            ['name' => 'viewer', 'display_name' => 'Viewer',        'description' => 'Read-only access to the dashboard'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
