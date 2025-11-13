<?php
declare(strict_types=1); 

namespace App\Core\Seeder; 
use App\Core\Seeder\Seeder; 
use App\Models\Product;
use App\Core\Traits\Faker; 

class ProductSeeder extends Seeder
{    
    use Faker;

    public function generateProducts() 
    {  
        for ($i=0; $i < 3000; $i++) { 
            $data = new Product;  
            $data->name = $this->name(); 
            $data->category = $this->categories();
            $data->description = $this->descriptions();
            $data->available = $this->availables() ? 1 : 0; // ternary operator, condition ? true : false;
            $data->brand = $this->brands();
            $data->code = $this->codes();
            $data->weight = $this->weights();
            $data->price = $this->prices();
            yield $data; 
        }          
    } 

    public function run() 
    {
        foreach ($this->generateProducts() as $value) {
            $value->store($value); 
        } 
        exit('seed all values with success!' . PHP_EOL);  
    }

} 