<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\School;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();

        if (!$school) {
            $this->command->warn('No school found. Skipping student seeder.');
            return;
        }

        $students = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'dob' => '2005-05-15',
                'father_name' => 'Robert Doe',
                'mother_name' => 'Jane Doe',
                'mobile' => '+1234567891',
                'email' => 'john.doe@example.com',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'dob' => '2006-08-20',
                'father_name' => 'Michael Smith',
                'mother_name' => 'Sarah Smith',
                'mobile' => '+1234567892',
                'email' => 'jane.smith@example.com',
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'dob' => '2005-12-10',
                'father_name' => 'David Johnson',
                'mother_name' => 'Emily Johnson',
                'mobile' => '+1234567893',
                'email' => 'alice.johnson@example.com',
            ],
        ];

        foreach ($students as $studentData) {
            $studentData['school_id'] = $school->id;
            Student::create($studentData);
        }

        $this->command->info('Sample students created.');
    }
}
