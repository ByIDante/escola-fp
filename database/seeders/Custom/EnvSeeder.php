<?php

declare(strict_types=1);

namespace Database\Seeders\Custom;

use Database\Factories\StudentFactory;
use Database\Factories\TeacherFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnvSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = '12345678';
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create user Ivan
        $ivan = UserFactory::new()->create(
            [
                'name' => 'Iván',
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'email' => 'ivan@test.com',
                'email_verified_at' => now(),
            ]
        );
        // Assign Ivan as a student
        StudentFactory::new()->create(
            [
                'user_id' => $ivan->id,
                'first_name' => 'Iván',
                'last_name' => 'Fernández',
            ]
        );

        // Create user Toni
        $toni = UserFactory::new()->admin()->create(
            [
                'name' => 'Toni',
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'email' => 'toni@test.com',
                'email_verified_at' => now(),
            ]
        );

        // Assign Toni as a teacher
        TeacherFactory::new()->create(
            [
                'user_id' => $toni->id,
                'first_name' => 'Toni',
                'last_name' => 'Jiménez',
            ]
        );
    }
}
