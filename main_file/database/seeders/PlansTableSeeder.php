<?php

namespace Database\Seeders;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::create(
            [
                'name' => 'Free Plan',
                'max_users' => 5,
                'max_projects' => 10,
                'monthly_price' => 0,
                'annual_price' => 0,
                'status' => 1,
            ]
        );
    }
}
