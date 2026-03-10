<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Asset::create([
            'type' => 'avatar',
            'filepath' => '/storage/media/2026-03-10/upload-user1-69afc4efe2f34.webp'
        ]);

        Asset::create([
            'type' => 'avatar',
            'filepath' => '/storage/media/2026-03-02/upload-user4-69a54c1fd0699.jpg'
        ]);
    }
}
