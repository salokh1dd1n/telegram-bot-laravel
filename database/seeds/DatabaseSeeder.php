<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         factory(City::class, 5)->create();
    }
}
