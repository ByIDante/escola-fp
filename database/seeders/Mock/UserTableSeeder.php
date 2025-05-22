<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        UserFactory::new()->count(40)->create();
    }
}
