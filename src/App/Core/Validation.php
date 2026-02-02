<?php 
declare(strict_types=1); 

namespace App\Core; 
use App\Models\Model; 

class Validation 
{
    public const RULE_REQUIRED = 'required';  
    private static array $model; 

    public static function validate(Model $model, array $rules) : array 
    {   
        $array = []; 
         foreach ($rules as $attribute => $rules) { 
          
            $value = $model->{$attribute};  // interpolazione dinamica, se $attribute = "name", diventa $this->name
            foreach ($rules as $rule) {

            if(!empty($_FILES[$attribute]) && $_FILES[$attribute]['error'] === 0)
            {
                continue; 
            }

              if ($rule === self::RULE_REQUIRED && !$value) {
                $array[$attribute] = self::addError($attribute,$rule); 
              } 
              
              if(!empty($_FILES[$attribute]) && $_FILES[$attribute]['error'] !== 0) 
              {
                $array[$attribute] = $this->addError($attribute,$rule); 
              }
           
           }          
         }         
        return $array;   
    } 

    private static function addError(string $attribute, string $rule) 
    {   
        return self::getErrors($attribute,$rule)[$rule]; 
    } 

    private static function getErrors(string $attribute, string $rule) 
    {
        if (isset(self::$model->alias) && isset(self::$model->alias[$attribute])) {
      
          $attribute = self::$model->alias[$attribute];
        }
        return [
            self::RULE_REQUIRED => $attribute.' is ' . $rule,
        ]; 
    }
}