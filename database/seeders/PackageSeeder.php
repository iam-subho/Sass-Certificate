<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for small institutions and startups. Includes basic features with limited certificate generation.',
                'monthly_certificate_limit' => 100,
                'duration_months' => 1,
                'price' => 29.99,
                'is_active' => true,
            ],
            [
                'name' => 'Basic Plan',
                'description' => 'Ideal for growing schools. Generate more certificates with priority support.',
                'monthly_certificate_limit' => 500,
                'duration_months' => 1,
                'price' => 99.99,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Plan',
                'description' => 'Best for established institutions. Higher limits and advanced features.',
                'monthly_certificate_limit' => 2000,
                'duration_months' => 1,
                'price' => 299.99,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'For large organizations. Unlimited certificates with dedicated support and custom features.',
                'monthly_certificate_limit' => 10000,
                'duration_months' => 1,
                'price' => 999.99,
                'is_active' => true,
            ],
            [
                'name' => 'Basic Annual',
                'description' => 'Annual subscription with 20% discount. Generate up to 500 certificates monthly.',
                'monthly_certificate_limit' => 500,
                'duration_months' => 12,
                'price' => 959.90, // 20% discount on 12 months
                'is_active' => true,
            ],
            [
                'name' => 'Professional Annual',
                'description' => 'Annual subscription with 25% discount. Generate up to 2000 certificates monthly.',
                'monthly_certificate_limit' => 2000,
                'duration_months' => 12,
                'price' => 2699.91, // 25% discount on 12 months
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        if ($this->command) {
            $this->command->info(count($packages) . ' packages created successfully.');
        }
    }
}
