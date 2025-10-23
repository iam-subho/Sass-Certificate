<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = CertificateTemplate::first();

        $school = School::create([
            'name' => 'Demo School',
            'email' => 'demo@school.com',
            'phone' => '+1234567890',
            'certificate_template_id' => $template->id,
            'signature_left_title' => 'Principal',
            'signature_middle_title' => 'Director',
            'signature_right_title' => 'Chairman',
            'is_active' => true,
        ]);

        // Create school admin for this school
        User::create([
            'name' => 'School Admin',
            'email' => 'school@example.com',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $this->command->info('Demo school created with admin:');
        $this->command->info('Email: school@example.com');
        $this->command->info('Password: password');
    }
}
