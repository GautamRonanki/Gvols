<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CollegeSeeder extends Seeder
{
    public function run(): void
    {
        $colleges = [
            'College of Nursing', 'College of Social Work', 'College of Business',
            'College of Engineering', 'College of Education', 'College of Arts & Sciences',
            'College of Law', 'College of Health Sciences', 'College of Communication',
        ];

        foreach ($colleges as $name) {
            College::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
