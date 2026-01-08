<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Request;
use App\Core\Validation; 

class Product extends Model
{   
    public ?string $name = null;
    public ?string $description = null;
    public ?string $category = null;
    public ?string $brand = null;
    public ?string $code = null;
    public ?string $image = null; 
    public float $price, $weight; 
    public int $available; 
    public $validation; 

    public function __construct() 
    {
        $this->validation = new Validation; 
    }

    protected function rules()
    {
        return [
            'name' => [Validation::RULE_REQUIRED], 
            'description' => [Validation::RULE_REQUIRED], 
            'image' => [Validation::RULE_REQUIRED]
        ];
    }

    public function validation(array $data) 
    {
        $this->loadData($data); 
        return $this->validation->validate($this, $this->rules()); 
    }
    
    public static function fetchAll() : array
    {   
        return self::fetchAllData('products');      
    } 

    public function store() : bool
    {   
        return $this->storeData($this,'products'); 
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

