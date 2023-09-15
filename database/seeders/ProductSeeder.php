<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("products")->insert([
            "id" => "1",
            "name" => "iPhone 14 Pro Max",
            "price" => 20000000,
            "category_id" => "SMARTPHONE"
        ]);

        DB::table("products")->insert([
            "id" => "2",
            "name" => "Samsung Galaxy S21 Ultra",
            "price" => 18000000,
            "category_id" => "SMARTPHONE"
        ]);
    }
}
