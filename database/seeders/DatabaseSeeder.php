<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CertificateTemplateSeeder::class,
            PackageSeeder::class,
            SuperAdminSeeder::class,
            SchoolSeeder::class,
            StudentSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
