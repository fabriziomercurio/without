<?php
declare(strict_types=1); 

namespace App\Core\Seeder; 
use App\Core\Seeder\Seeder; 
use App\Models\Product;
use App\Models\Multimedia; 
use App\Core\Traits\Faker; 
use App\Core\Transaction; 

class ProductSeeder extends Seeder
{    
    use Faker;

    public function generateProducts() 
    {  
        for ($i=0; $i < 30; $i++) {
            $data = new Product;             

            $createMultimedia = rand(1,100) <= 40; 

            $multi = null; 

            if ($createMultimedia) {
                $multi = new Multimedia; 
                $multi->multi_name = rand(1,100) <= 50 ? $this->randomValues($this->name_images) : null;
            }

            $data->name = $this->randomValues($this->names); 
            $data->category = $this->randomValues($this->categories);
            $data->descr = $this->randomValues($this->descriptions);
            $data->available = $this->randomValues($this->availables) ? 1 : 0; // ternary operator, condition ? true : false;
            $data->brand = $this->randomValues($this->brands);
            $data->code = $this->randomValues($this->codes);
            $data->weight = $this->randomValues($this->weights);
            $data->price = $this->randomValues($this->prices);

            yield [
                'product' => $data, 
                'multimedia' => $multi
            ]; 
        }          
    } 

    public function run() 
    {
        Transaction::beginTransaction(); 

        try {
            foreach ($this->generateProducts() as $value) {

            $multi = $value['multimedia']; 
            $product = $value['product'];  

            if ($multi !== null) {
                $multi->store();
                $product->xMultimediaId = $multi->id; 
            }         

            $product->store();            
        }             
            Transaction::commit();
            echo json_encode("seed all values with success!");
        } catch (\Throwable $th) {
            echo json_encode($th->getMessage());
            Transaction::rollBack();
        }        
    }
} 