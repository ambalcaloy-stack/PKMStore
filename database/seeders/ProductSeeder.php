<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            ['product_name' => 'PKM Notebook', 'description' => 'School notebook', 'price' => 120.00, 'stock_quantity' => 50, 'status' => 'Available'],
            ['product_name' => 'Ballpen Set', 'description' => 'Set of 5 pens', 'price' => 85.00, 'stock_quantity' => 25, 'status' => 'Available'],
            ['product_name' => 'Student Planner', 'description' => 'Academic planner', 'price' => 260.00, 'stock_quantity' => 10, 'status' => 'Low Stock'],
            ['product_name' => 'Research Folder', 'description' => 'Folder for notes and papers', 'price' => 140.00, 'stock_quantity' => 8, 'status' => 'Low Stock'],
        ]);
    }
}
