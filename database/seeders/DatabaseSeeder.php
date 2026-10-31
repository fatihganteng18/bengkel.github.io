<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motorcycle;
use App\Models\Product;
use App\Models\Store;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Motorcycle::insert([
            ['brand' => 'Honda', 'model_name' => 'Vario 160', 'front_tire_size' => '100/80-14', 'rear_tire_size' => '120/70-14', 'created_at' => now(), 'updated_at' => now()],
            ['brand' => 'Honda', 'model_name' => 'BeAT Street', 'front_tire_size' => '80/90-14', 'rear_tire_size' => '90/90-14', 'created_at' => now(), 'updated_at' => now()],
            ['brand' => 'Yamaha', 'model_name' => 'NMAX 155', 'front_tire_size' => '110/70-13', 'rear_tire_size' => '130/70-13', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Product::insert([
            ['name' => 'Planetoil Matic 10W-30', 'slug' => 'planetoil-10w-30', 'category' => 'oil', 'price' => 55000, 'stock' => 50, 'description' => 'Oli mesin matic.', 'image' => '', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ban Corsa Platinum R26', 'slug' => 'corsa-r26', 'category' => 'tire', 'price' => 285000, 'stock' => 25, 'description' => 'Ban tubeless belakang.', 'image' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Store::insert([
            ['name' => 'Planet Ban Ahmad Yani', 'address' => 'Jl. Ahmad Yani No. 124', 'city' => 'Surabaya', 'phone' => '0812345678', 'latitude' => -7.28, 'longitude' => 112.74, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
