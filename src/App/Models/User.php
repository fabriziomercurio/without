<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 

class User extends Model
{
    public string $firstname; 
    public string $lastname;    

    protected function rules() 
    {
        return [
            'firstname' => [self::RULE_REQUIRED], 
            'lastname' => [self::RULE_REQUIRED]
        ];   
    }

    

    
}