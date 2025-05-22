<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserRoleEnum;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Teacher>
 */
final class TeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Get IDs of users already assigned to students
        $usedUserIds = Student::pluck('user_id')->toArray();

        // Search for a user that is not assigned to any student
        $availableUser = User::whereNotIn('id', $usedUserIds)->inRandomOrder()->first();

        // If it's not possible to find a user that is not assigned to any student, create a new one
        if (!$availableUser) {
            $availableUser = User::factory()->create();
        }

        // Update the user's role to ADMIN
        $availableUser->update(['role' => UserRoleEnum::ADMIN->value]);

        return [
            'user_id' => $availableUser->id,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
        ];
    }
}
