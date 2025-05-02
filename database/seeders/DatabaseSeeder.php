<?php

namespace Database\Seeders;

use App\Models\PartnerCompany;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('123456'),
            'phone' => '1234567890',

        ]);
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
            'phone' => '1234567891',
        ]);

        PartnerCompany::create(['name' => 'Company A', 'active' => true, 'email' => 'company1@a.com']);
        PartnerCompany::create(['name' => 'Company B', 'active' => false, 'email' => 'company2@a.com']);
        PartnerCompany::create([
            'name' => 'Company C',
            'active' => true,
            'email' => 'company3@a.com'
        ]);
    }
}
