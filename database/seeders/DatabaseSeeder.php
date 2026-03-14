<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and literature'],
            ['name' => 'Sports', 'description' => 'Sports and outdoor equipment'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
            ]);
        }

        $products = [
            ['category_id' => 1, 'name' => 'Laptop Pro 15', 'price' => 15000000, 'stock' => 10],
            ['category_id' => 1, 'name' => 'Wireless Headphones', 'price' => 850000, 'stock' => 25],
            ['category_id' => 1, 'name' => 'Smartphone X12', 'price' => 8500000, 'stock' => 15],
            ['category_id' => 2, 'name' => 'Classic T-Shirt', 'price' => 150000, 'stock' => 100],
            ['category_id' => 2, 'name' => 'Slim Fit Jeans', 'price' => 350000, 'stock' => 50],
            ['category_id' => 3, 'name' => 'Laravel Up & Running', 'price' => 250000, 'stock' => 30],
            ['category_id' => 3, 'name' => 'Clean Code', 'price' => 200000, 'stock' => 20],
            ['category_id' => 4, 'name' => 'Running Shoes', 'price' => 750000, 'stock' => 40],
        ];

        foreach ($products as $prod) {
            Product::create([
                'category_id' => $prod['category_id'],
                'name' => $prod['name'],
                'slug' => Str::slug($prod['name']),
                'description' => 'Sample product description for ' . $prod['name'],
                'price' => $prod['price'],
                'stock' => $prod['stock'],
                'is_active' => true,
            ]);
        }
    }
}
