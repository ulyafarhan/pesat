<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CameraSeeder::class,
        ]);

        User::firstOrCreate(
            ['email' => 'admin@pesat.local'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'wh@pesat.local'],
            [
                'name' => 'WH Officer',
                'password' => 'password',
                'role' => 'wh_officer',
            ]
        );

        AdminSetting::firstOrCreate(
            ['key' => 'break_mode_active'],
            ['value' => 'false']
        );

        AdminSetting::firstOrCreate(
            ['key' => 'break_start_time'],
            ['value' => '12:00']
        );

        AdminSetting::firstOrCreate(
            ['key' => 'break_end_time'],
            ['value' => '14:00']
        );
    }
}
