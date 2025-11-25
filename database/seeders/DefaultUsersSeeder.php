<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('123123123');

        // Varsayılan company_id (eğer companies tablosu varsa, ilk şirketi kullan)
        $companyId = DB::table('companies')->first()->id ?? 1;

        $users = [
            [
                'name' => 'Mustafa Çiftci',
                'email' => 'admin@example.com',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Sistem Yöneticisi',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Birim Amiri',
                'email' => 'birim_amiri@example.com',
                'password' => $password,
                'role' => 'birim_amiri',
                'position' => 'Birim Amiri',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Alan Amiri',
                'email' => 'alan_amiri@example.com',
                'password' => $password,
                'role' => 'alan_amiri',
                'position' => 'Alan Amiri',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ISG Uzmanı',
                'email' => 'isg_uzmani@example.com',
                'password' => $password,
                'role' => 'isg_uzmani',
                'position' => 'İş Sağlığı ve Güvenliği Uzmanı',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'İşveren Vekili',
                'email' => 'isveren_vekili@example.com',
                'password' => $password,
                'role' => 'isveren_vekili',
                'position' => 'İşveren Vekili',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Çalışan',
                'email' => 'calisan@example.com',
                'password' => $password,
                'role' => 'calisan',
                'position' => 'Çalışan',
                'company_id' => $companyId,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Mevcut kullanıcıları kontrol et ve sadece yoksa ekle
        foreach ($users as $userData) {
            $exists = DB::table('users')->where('email', $userData['email'])->exists();

            if (!$exists) {
                DB::table('users')->insert($userData);
                $this->command->info("✓ {$userData['email']} oluşturuldu");
            } else {
                $this->command->warn("⚠ {$userData['email']} zaten mevcut");
            }
        }

        $this->command->info('
Kullanıcılar başarıyla oluşturuldu!');
        $this->command->info('Şifre: 123123123');
    }
}
