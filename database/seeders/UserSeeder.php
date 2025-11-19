<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $locations = [
            'Metro Manila' => ['Manila', 'Quezon City'],
            'Cebu' => ['Cebu City', 'Mandaue'],
            'Davao' => ['Davao City'],
            'Pampanga' => ['Angeles', 'San Fernando'],
            'Laguna' => ['Sta. Rosa'],
            'Cavite' => ['Imus'],
            'Bohol' => ['Tagbilaran']
        ];

        $admin = User::create([
            'username' => 'admin',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'test@gmail.com',
            'password' => Hash::make('developer'),
            'gender' => 'M'
        ]);
        $admin->assignRole('ADMIN');
        
        $users = User::factory(10)->create();

        $i = 1;

        foreach ($users as $user) {
            $province = $faker->randomElement(array_keys($locations));
            $cities = $locations[$province];
            $city = count($cities) > 0 ? $faker->randomElement($cities) : $province;

            $user->update([
                'username' => 'user' . $i,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'birthdate' => $faker->dateTimeBetween('-40 years', '-15 years'),
                'gender' => $faker->randomElement(['M', 'F', 'Other', 'Prefer not to say', 'M', 'F', 'M', 'F']),
                'city' => $city,
                'province' => $province,
                'country' => 'Philippines',
                'contact_number' => '+63' . $faker->numberBetween(9000000000, 9999999999),
                'password' => Hash::make('user'),
            ]);
            $user->assignRole('USER');
            $i+=1;
        }
    }
}
