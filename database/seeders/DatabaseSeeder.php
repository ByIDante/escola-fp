<?php

namespace Database\Seeders;

use Database\Seeders\Custom\EnvSeeder;
use Database\Seeders\Mock\EvaluationTableSeeder;
use Database\Seeders\Mock\ModuleTableSeeder;
use Database\Seeders\Mock\StudentTableSeeder;
use Database\Seeders\Mock\TeacherTableSeeder;
use Database\Seeders\Mock\UnitTableSeeder;
use Database\Seeders\Mock\UserTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run base seeders
        $this->call([
            UserTableSeeder::class,
            EnvSeeder::class,
            StudentTableSeeder::class,
            TeacherTableSeeder::class,
            ModuleTableSeeder::class,
            UnitTableSeeder::class,
            EvaluationTableSeeder::class,
        ]);

    }
}
