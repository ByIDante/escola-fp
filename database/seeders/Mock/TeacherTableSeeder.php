<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Seeder;

class TeacherTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        TeacherFactory::new()->count(20)->create();
    }
}
