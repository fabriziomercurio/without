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
    public ?int $age = NULL; 
    public ?string $city = NULL; 

    public static string $table = 'users'; 

    protected function getTable(): string 
    {  
        return self::$table;    
    }

    protected function rules() 
    {
        return [
            'firstname' => [Validation::RULE_REQUIRED], 
            'lastname' => [Validation::RULE_REQUIRED], 
            'email' => [[Validation::RULE_REQUIRED],[Validation::RULE_EMAIL_UNIQUE]], 
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

    protected array $casts = ['age'=>'int'];

    public function store() : string|false 
    {   
        $data = $this->getDatabaseAttributes(); 
        return $this->storeData($data,self::$table); 
    }
    
}