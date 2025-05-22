<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use App\Models\Module;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Unit;
use Database\Factories\EvaluationFactory;
use Illuminate\Database\Seeder;

class EvaluationTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        // Verify that necessary data exists before creating evaluations
        $studentsCount = Student::count();
        $teachersCount = Teacher::count();
        $modulesCount = Module::count();

        // If there are no data, create some to make the seed work
        if ($studentsCount == 0) {
            Student::factory()->count(10)->create();
        }

        if ($teachersCount == 0) {
            Teacher::factory()->count(5)->create();
        }

        if ($modulesCount == 0) {
            Module::factory()->count(8)->create();
        }

        // Create units for each module if they don't exist
        $modules = Module::all();
        foreach ($modules as $module) {
            // Verify if this module already has units
            $moduleUnitsCount = $module->units()->count();
            if ($moduleUnitsCount == 0) {
                // Assign teachers to each unit
                $teachers = Teacher::all()->random(min(3, Teacher::count()));

                foreach ($teachers as $index => $teacher) {
                    Unit::factory()->create([
                        'module_id' => $module->id,
                        'teacher_id' => $teacher->id,
                        'title' => "Unidad " . ($index + 1) . " - " . $module->name
                    ]);
                }
            }
        }

        // Standard evaluations
        EvaluationFactory::new()->count(30)->create();

        // Some evaluations with high scores
        EvaluationFactory::new()->count(10)->highScore()->create();

        // Some evaluations with low scores
        EvaluationFactory::new()->count(10)->lowScore()->create();
    }
}
