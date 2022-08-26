<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('1234'),
                'type' => 'admin',
                'lang' => 'en',
                'is_active' => '1',
            ]
        );

        // create new email template notification
        $admin->defaultEmail();

        $owner = User::create(
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'password' => Hash::make('1234'),
                'type' => 'owner',
                'lang' => 'en',
                'created_by' => $admin->id,
                'is_active' => '1',
            ]
        );

        $owner->assignPlan(1);
    }
}
