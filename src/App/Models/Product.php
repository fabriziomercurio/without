<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Request;
use App\Core\Validation; 

class Product extends Model
{   
    public ?string $name = null;
    public ?string $descr = null;
    public ?string $category = null;
    public ?string $brand = null;
    public ?string $code = null;
    public ?string $image = null; 
    public ?int $xMultimediaId = null;
    public float $price, $weight; 
    public int $available; 
    
    public array $fillable = ['name','description','xMultimediaId']; 

    public static string $table = 'products'; 

    protected function getTable(): string 
    {  
        return self::$table;    
    }

    protected function fillable() : array 
    {
        return ['name','description','xMultimediaId'] ; 
    } 

    protected array $alias = [
        'description' => 'descr'
    ];

    protected function rules()
    {
        return [
            'name' => [Validation::RULE_REQUIRED], 
            'descr' => [Validation::RULE_REQUIRED]
        ];
    }

    public function validation(array $data) : array 
    {   
        $this->loadData($data); 
        return Validation::validate($this, $this->rules()); 
    }
    
    public static function fetchAll() : array
    {   
        return self::fetchAllData(self::$table);      
    } 

    public function store() : string|false 
    {   
        $data = $this->getDatabaseAttributes();
        return $this->storeData($data,self::$table); 
    } 

    public static function edit(int $id) : bool | array 
    {
        return self::editRecord($id,self::$table); 
    } 

    public function update(int $id, Request $request) : bool 
    {   
        $data = $this->getMapRequestAttributes($request->getBody());
        return self::updateRecord($id, $data, self::$table); 
    }

    public static function delete(int $id) : bool
    {
       return self::deleteRecord($id,$this->table); 
    }
}


?> 

