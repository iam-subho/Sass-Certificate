<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use App\Models\CertificateTemplate;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = CertificateTemplate::all();
        $package = Package::where('name', 'Basic Plan')->first() ?? Package::first();

        if (!$package) {
            if ($this->command) {
                $this->command->error('No packages found. Please run PackageSeeder first.');
            }
            return;
        }

        $school = School::create([
            'name' => 'Demo School',
            'email' => 'demo@school.com',
            'phone' => '+1234567890',
            'package_id' => $package->id,
            'monthly_certificate_limit' => $package->monthly_certificate_limit,
            'plan_type' => 'free',
            'signature_left_title' => 'Principal',
            'signature_middle_title' => 'Director',
            'signature_right_title' => 'Chairman',
            'is_active' => true,
        ]);

        // Attach multiple certificate templates to the school
        if ($templates->isNotEmpty()) {
            // Attach all available templates to demo school
            $school->certificateTemplates()->attach($templates->pluck('id'));
        }

        // Create school admin for this school
        User::create([
            'name' => 'School Admin',
            'email' => 'school@example.com',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        if ($this->command) {
            $this->command->info('Demo school created with admin:');
            $this->command->info('Email: school@example.com');
            $this->command->info('Password: password');
            $this->command->info('Templates attached: ' . $templates->count());
        }
    }
}
