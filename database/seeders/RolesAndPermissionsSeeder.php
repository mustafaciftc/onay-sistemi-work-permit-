<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'birim_amiri',
            'alan_amiri',
            'isg_uzmani',
            'isveren_vekili',
            'user',
            'manager',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        $this->command->info('Roller başarıyla oluşturuldu (Spatie standartlarına uygun).');
    }
}
