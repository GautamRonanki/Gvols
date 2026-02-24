<?php

namespace Database\Seeders;

use App\Models\AreaOfInterest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreaOfInterestSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Agriculture Science', 'Arts & Science', 'Business',
            'Business Law & Leadership', 'Communication & Media', 'Computer Science',
            'Computer Science & Data Technology', 'Education & Human Development',
            'Engineering', 'Health Sciences', 'Law', 'Public Administration', 'Social Work',
        ];

        foreach ($areas as $name) {
            AreaOfInterest::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
