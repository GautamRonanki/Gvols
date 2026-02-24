<?php

namespace Database\Seeders;

use App\Models\ProgramType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Bachelor', 'Certificate', 'Doctorate', 'External RFI',
            'Haslam', "Master's", 'Micro-credentials', "Postmaster's",
        ];

        foreach ($types as $name) {
            ProgramType::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
