<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedCategories();
        $this->seedItems();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );
    }

    private function seedItems()
    {
        $items = [
            ['title' => 'Laptop', 'description' => 'High performance laptop', 'price' => 45000, 'status' => 1, 'category_id' => 1],
            ['title' => 'T-Shirt', 'description' => 'Cotton t-shirt', 'price' => 500, 'status' => 1, 'category_id' => 2],
            ['title' => 'Novel', 'description' => 'Best selling novel', 'price' => 250, 'status' => 1, 'category_id' => 3],
            ['title' => 'Garden Tools', 'description' => 'Set of 5 tools', 'price' => 1200, 'status' => 0, 'category_id' => 4],
            ['title' => 'Smartphone', 'description' => 'Android smartphone', 'price' => 15000, 'status' => 1, 'category_id' => 1],
            ['title' => 'Jeans', 'description' => 'Denim jeans', 'price' => 800, 'status' => 1, 'category_id' => 2],
            ['title' => 'Textbook', 'description' => 'Engineering textbook', 'price' => 600, 'status' => 0, 'category_id' => 3],
            ['title' => 'Plant Pot', 'description' => 'Ceramic plant pot', 'price' => 300, 'status' => 1, 'category_id' => 4],
            ['title' => 'Headphones', 'description' => 'Wireless headphones', 'price' => 2000, 'status' => 1, 'category_id' => 1],
            ['title' => 'Sneakers', 'description' => 'Running shoes', 'price' => 1500, 'status' => 0, 'category_id' => 2],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['title' => $item['title']], $item);
        }
    }

    private function seedCategories()
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic gadgets and devices'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel items'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Garden', 'description' => 'Home decor and gardening tools'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
