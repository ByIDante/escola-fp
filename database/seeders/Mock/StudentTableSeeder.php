<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use Database\Factories\StudentFactory;
use Illuminate\Database\Seeder;

class StudentTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        StudentFactory::new()->count(20)->create();
    }
}
