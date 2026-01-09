<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Validation;

class Multimedia extends Model 
{
    public ?string $name = null; 
    public $validation;

    public function __construct() 
    {
        $this->validation = new Validation; 
    }

    public array $fillable = ['name']; 

    public array $alias = [
        'name' => 'multi_name'
    ];

    protected function rules()
    {
        return [
            'name' => [Validation::RULE_REQUIRED]
        ];
    }

    public function loadData($data) 
    {
        if (isset($data['multi_name'])) {
           $this->name = $data['multi_name'];
        }
    }

    public function validation(array $data) 
    {
        $this->loadData($data); 
        return $this->validation->validate($this, $this->rules()); 
    }

    public function store() : string|false 
    {   
        return $this->storeData($this,'multimedia'); 
    }
}


?>