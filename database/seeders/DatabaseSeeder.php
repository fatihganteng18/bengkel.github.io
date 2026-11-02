<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use App\Models\Store;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(CashierUserSeeder::class);

        foreach ([
            ['brand' => 'Honda', 'model_name' => 'Vario 160', 'front_tire_size' => '100/80-14', 'rear_tire_size' => '120/70-14'],
            ['brand' => 'Honda', 'model_name' => 'BeAT Street', 'front_tire_size' => '80/90-14', 'rear_tire_size' => '90/90-14'],
            ['brand' => 'Yamaha', 'model_name' => 'NMAX 155', 'front_tire_size' => '110/70-13', 'rear_tire_size' => '130/70-13'],
        ] as $motorcycleData) {
            $motorcycle = Motorcycle::query()->firstOrNew([
                'brand' => $motorcycleData['brand'],
                'model_name' => $motorcycleData['model_name'],
            ]);
            $motorcycle->forceFill($motorcycleData)->save();
        }

        $this->call(ProductSeeder::class);

        foreach ([
            ['name' => 'Planet Ban Ahmad Yani', 'address' => 'Jl. Ahmad Yani No. 124', 'city' => 'Surabaya', 'phone' => '0812345678', 'latitude' => -7.28, 'longitude' => 112.74],
        ] as $storeData) {
            $store = Store::query()->firstOrNew(['name' => $storeData['name']]);
            $store->forceFill($storeData)->save();
        }
    }
}
