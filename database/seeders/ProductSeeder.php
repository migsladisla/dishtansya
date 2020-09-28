<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'GeForce GTX 1080Ti',
                'stock' => 5,
            ],
            [
                'name' => 'GeForce GTX 1660Ti',
                'stock' => 15,
            ],
            [
                'name' => 'GeForce RTX 2060',
                'stock' => 50,
            ],
            [
                'name' => 'GeForce RTX 2080',
                'stock' => 75,
            ],
            [
                'name' => 'GeForce RTX 3070',
                'stock' => 100,
            ],
        ];

        foreach($data as $item) {
            Product::create($item);
        }
    }
}
