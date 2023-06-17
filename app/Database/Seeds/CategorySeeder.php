<?php

namespace App\Database\Seeds;

use App\Models\Category;
use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
	public function run()
	{
        $data = [
            ['name' =>  'Sin Categoria']
        ];

        foreach ($data as $item):
            $category = new Category();
            $category->insert($item);
        endforeach;
	}
}
