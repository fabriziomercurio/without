<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 

class User extends Model
{
    public string $firstname; 
    public string $lastname;    
    public string $email; 
    public string $password;
    public string $age; 
    public string $city; 

    protected function rules() 
    {
        return [
            'firstname' => [self::RULE_REQUIRED], 
            'lastname' => [self::RULE_REQUIRED]
        ];   
    }

    

    
}