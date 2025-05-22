<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Module;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
final class EvaluationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Evaluation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Get a random student or create a new one
        $student = Student::inRandomOrder()->first();
        if (!$student) {
            $student = Student::factory()->create();
        }

        // Get a random teacher or create a new one
        $teacher = Teacher::inRandomOrder()->first();
        if (!$teacher) {
            $teacher = Teacher::factory()->create();
        }

        // Get a random module or create a new one
        $module = Module::inRandomOrder()->first();
        if (!$module) {
            $module = Module::factory()->create();
        }

        // Get a random unit or create a new one for the module
        $unit = $module->units()->inRandomOrder()->first();
        if (!$unit) {
            $unit = Unit::factory()->create([
                'module_id' => $module->id,
                'teacher_id' => $teacher->id,
            ]);
        }

        return [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'module_id' => $module->id,
            'unit_id' => $unit->id,
            'score' => fake()->randomFloat(1, 0, 10),
            'comments' => fake()->boolean(80) ? fake()->paragraph() : null,
            'evaluation_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Estado para evaluaciones con puntuación alta
     *
     * @return Factory
     */
    public function highScore(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'score' => fake()->randomFloat(1, 8.5, 10),
        ]);
    }

    /**
     * Estado para evaluaciones con puntuación baja
     *
     * @return Factory
     */
    public function lowScore(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'score' => fake()->randomFloat(1, 0, 4.9),
        ]);
    }
}
