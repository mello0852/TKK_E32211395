<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\sensor_data;
use App\Models\Control;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Melloz',
            'username' => 'mello',
            'email' => 'coollanlutfi@gmail.com',
            'password' => bcrypt('11111111')
        ]);

        Monitoring::create([
            //'notification' => false, 
            'voltage' => 0,
            'power' => 0,
            'power_factor' => 0,
            'energy' => 0,
            'current' => 0,
            'biaya' => 0,
        ]);

        Control::factory(24)->create();

    }
}
