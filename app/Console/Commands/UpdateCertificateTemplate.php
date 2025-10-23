<?php

namespace App\Console\Commands;

use App\Models\CertificateTemplate;
use Illuminate\Console\Command;
use Database\Seeders\CertificateTemplateSeeder;

class UpdateCertificateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate:update-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update certificate template to the latest design (includes event info and conditional signatures)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating certificate template...');

        // Delete old templates
        $deleted = CertificateTemplate::where('name', 'Classic Certificate Template')->delete();
        if ($deleted) {
            $this->info("Deleted old 'Classic Certificate Template'");
        }

        // Check if modern template exists
        $existing = CertificateTemplate::where('name', 'Modern Professional Certificate')->first();

        if ($existing) {
            $this->warn('Modern Professional Certificate already exists.');

            if ($this->confirm('Do you want to recreate it?')) {
                $existing->delete();
                $this->info('Deleted existing modern template.');
            } else {
                $this->info('No changes made.');
                return 0;
            }
        }

        // Create new template
        $seeder = new CertificateTemplateSeeder();
        $seeder->run();

        $this->info('✓ Certificate template updated successfully!');
        $this->info('✓ New template: "Modern Professional Certificate"');
        $this->info('✓ Features: Tailwind CSS, modern gradients, professional typography');

        return 0;
    }
}
