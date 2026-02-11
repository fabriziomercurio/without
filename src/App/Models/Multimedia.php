<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Validation;

class Multimedia extends Model 
{
    public ?string $multi_name = null;
    public ?int $id = null; 
    
    public string $table = 'multimedia'; 

    protected function getTable(): string 
    {  
        return $this->table;    
    }

    protected function fillable() : array 
    {
        return ['name'] ; 
    }

    protected array $alias = [
        'name' => 'multi_name'
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
       $id = $this->storeData($data,$this->table);  
       $this->id = $id !== false ? (int)$id : false; 
       return $this->id; 
    }
}


?>