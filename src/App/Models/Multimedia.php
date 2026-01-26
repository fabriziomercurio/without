<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Validation;

class Multimedia extends Model 
{
    public ?string $multi_name = null; 
    public $validation;

    public function __construct() 
    {
        $this->validation = new Validation; 
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

    public function loadData($data) 
    {
        foreach ($data as $key => $value) {
            if (property_exists($this,$key)) {
              $this->{$key} = $value;         
            }
        } 
    }

    public function validation(array $data) 
    {
        $this->loadData($data); 
        return $this->validation->validate($this, $this->rules()); 
    }

    public function store() : string|false 
    {      
       $data = $this->getDatabaseAttributes();
       return $this->storeData($data,'multimedia');  
    }
}


?>