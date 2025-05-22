<?php

declare(strict_types=1);

namespace Database\Seeders\Mock;

use Database\Factories\ModuleFactory;
use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run(): void
    {
        ModuleFactory::new()->count(15)->create();
    }
}
