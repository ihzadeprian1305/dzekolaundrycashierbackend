<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Package;
use App\Models\Stuff;
use App\Models\User;
use App\Models\UserDatum;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(5)->create();
        UserDatum::factory(5)->create();
        UserLevel::create([
            'name' => 'Administrator'
        ]);
        UserLevel::create([
            'name' => 'Pemilik'
        ]);
        UserLevel::create([
            'name' => 'Karyawan'
        ]);
        // UserLevel::create([
        //     'name' => 'Pelanggan'
        // ]);
        Package::create([
            'name' => 'Cuci Kering Setrika',
            'price' => 4500,
            'type' => 'Kiloan'
        ]);
        Package::create([
            'name' => 'Cuci Kering Wangi',
            'price' => 3500,
            'type' => 'Kiloan'
        ]);
        Package::create([
            'name' => 'Cuci Basah',
            'price' => 3000,
            'type' => 'Kiloan'
        ]);
        Package::create([
            'name' => 'Setrika',
            'price' => 3500,
            'type' => 'Kiloan'
        ]);
        Package::create([
            'name' => 'Kilat',
            'price' => 5000,
            'type' => 'Kiloan'
        ]);
        Package::create([
            'name' => 'Bed Cover Single',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Bed Cover Double',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Selimut Kecil',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Selimut Besar',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Keset',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Handuk',
            'price' => 5000,
            'type' => 'Potongan'
        ]);
        Package::create([
            'name' => 'Sprei',
            'price' => 6000,
            'type' => 'Potongan'
        ]);
        Stuff::create([
            'name' => 'Detergen 1.8 kg',
            'price' => 25000,
            'type' => 'Buah'
        ]);
        Stuff::create([
            'name' => 'Pengharum',
            'price' => 20000,
            'type' => 'Liter'
        ]);
        Stuff::create([
            'name' => 'Selotip Bening',
            'price' => 12000,
            'type' => 'Buah'
        ]);
    }
}
