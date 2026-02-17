<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Validation;

class Multimedia extends Model 
{
    public ?string $multi_name = null;
    public ?int $id = null; 
    
    public static string $table = 'multimedia'; 

    protected function getTable(): string 
    {  
        return self::$table;    
    }

    protected function fillable() : array 
    {
        return ['filename'] ; 
    }

    protected array $alias = [
        'filename' => 'multi_name'
    ];

    protected function rules()
    {
         return [];        
    }

    public function validation(array $data) : array 
    {
        $this->loadData($data); 
        return Validation::validate($this,$this->rules());
    }

    public function store() : int|false 
    {      
       $data = $this->getDatabaseAttributes();
       $id = $this->storeData($data,self::$table);  
       $this->id = $id !== false ? (int)$id : false; 
       return $this->id; 
    }

    public static function edit(int $id) 
    {
       return self::editRecord($id,self::$table); 
    }
}


?>