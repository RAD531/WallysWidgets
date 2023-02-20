<?php

namespace Database\Seeders;

use App\Models\PackSizes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        PackSizes::insert([
            ['packSize' => 250, 'created_at' => $now, 'updated_at' => $now],
            ['packSize' => 500, 'created_at' => $now, 'updated_at' => $now],
            ['packSize' => 1000, 'created_at' => $now, 'updated_at' => $now],
            ['packSize' => 2000, 'created_at' => $now, 'updated_at' => $now],
            ['packSize' => 5000, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
