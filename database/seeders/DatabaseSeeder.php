<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\WorkPermitForm;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        $this->call([
            RolesAndPermissionsSeeder::class,
            DefaultUsersSeeder::class,
        ]);

        // Demo kullanıcılar
        $user1 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Demo şirketler
        $company1 = Company::create([
            'name' => 'A Firması',
            'email' => 'a@firma.com',
            'phone' => '0555 555 55 55',
            'address' => 'İstanbul, Türkiye',
        ]);

        $company2 = Company::create([
            'name' => 'B Firması',
            'email' => 'b@firma.com',
            'phone' => '0555 555 55 56',
            'address' => 'Ankara, Türkiye',
        ]);

        // Demo iş izinleri
        WorkPermitForm::create([
            'company_id' => $company1->id,
            'created_by' => $user1->id,
            'title' => 'Elektrik Panosu Bakımı',
            'work_type' => 'elektrik',
            'work_description' => 'Ana elektrik panosunun yıllık bakım ve kontrolü',
            'location' => 'Ana Bina - Zemin Kat',
            'risks' => json_encode(['Elektrik riski', 'Yangın riski']),
            'control_measures' => json_encode(['Enerji kesildi', 'Uyarı tabelası konuldu']),
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'status' => 'pending_unit_approval',
        ]);
    }
}
