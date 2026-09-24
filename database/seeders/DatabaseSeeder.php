<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@pkm.edu'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $products = [
            ['product_name' => 'PKM Notebook', 'price' => 120.00, 'stock_quantity' => 50, 'status' => 'Available'],
            ['product_name' => 'Ballpen Set', 'price' => 85.00, 'stock_quantity' => 25, 'status' => 'Available'],
            ['product_name' => 'Student Planner', 'price' => 260.00, 'stock_quantity' => 10, 'status' => 'Low Stock'],
            ['product_name' => 'Research Folder', 'price' => 140.00, 'stock_quantity' => 8, 'status' => 'Low Stock'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['product_name' => $product['product_name']],
                $product
            );
        }
    }
}
