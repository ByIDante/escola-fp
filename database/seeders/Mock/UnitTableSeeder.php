<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use Database\Factories\UnitFactory;
use Illuminate\Database\Seeder;

class UnitTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        UnitFactory::new()->count(40)->create();
    }
}
