<?php

namespace Database\Seeders;

use App\Models\AdmissionTerm;
use Illuminate\Database\Seeder;

class AdmissionTermSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Fall', 'Spring', 'Summer'] as $name) {
            AdmissionTerm::firstOrCreate(['name' => $name]);
        }
    }
}
