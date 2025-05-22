<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Module;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Unit>
 */
final class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'module_id' => Module::inRandomOrder()->first()?->id ?? ModuleFactory::new()->create()->id,
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? TeacherFactory::new()->create()->id,
        ];
    }
}
