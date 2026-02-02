<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 
use App\Core\Validation;

class User extends Model
{
    public ?string $firstname = NULL;
    public ?string $lastname = NULL;   
    public ?string $email = NULL;
    public ?string $password = NULL;
    public ?string $age = NULL; 
    public ?string $city = NULL; 

    protected function rules() 
    {
        return [
            'firstname' => [Validation::RULE_REQUIRED], 
            'lastname' => [Validation::RULE_REQUIRED], 
            'email' => [Validation::RULE_REQUIRED], 
            'password' => [Validation::RULE_REQUIRED], 
        ];   
    } 

    public function validation(array $data) 
    {
        $this->loadData($data); 
        return Validation::validate($this, $this->rules()); 
    }

    public function fillable() : array 
    {
        return ['firstname','lastname','email','password','age','city']; 
    } 

    public function store() : string|false 
    {   
        $data = $this->getDatabaseAttributes(); 
        return $this->storeData($data,'users'); 
    }

    

    
}