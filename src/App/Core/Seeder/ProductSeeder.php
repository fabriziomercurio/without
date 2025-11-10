<?php
declare(strict_types=1); 

namespace App\Core\Seeder; 
use App\Core\Seeder\Seeder; 
use App\Models\Product;

class ProductSeeder extends Seeder
{         
    public function run() 
    {
        $data = new Product; 
        $data->firstname = 'test_name'; 
        // $data->surname = 'test_surname';
        // $data->age = '39';
        // $data->city = 'Torino';
        $data->store($data);  
    }
}