<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Request;

class Product extends Model
{   
    public ?string $name = null;
    public ?string $description = null;
    public ?string $category = null;
    public ?string $brand = null;
    public ?string $code = null;
    public float $price, $weight; 
    public int $available; 

    protected function rules()
    {
        return [
            'name' => [self::RULE_REQUIRED], 
            'description' => [self::RULE_REQUIRED]
        ];
    }
    
    public static function fetchAll() : array
    {   
        return self::fetchAllData('products');      
    } 

    public function store(object $data) : bool
    {   
        return $this->storeData($data,'products'); 
    } 

    public static function edit(int $id) : bool | array 
    {
        return self::editRecord($id, 'products'); 
    } 

    public static function update(int $id, Request $request) : bool 
    {
        return self::updateRecord($id, $request,'products'); 
    }

    public static function delete(int $id) : bool
    {
       return self::deleteRecord($id,'products'); 
    }
}


?> 

