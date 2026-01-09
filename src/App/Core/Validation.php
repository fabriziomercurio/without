<?php 
declare(strict_types=1); 

namespace App\Core; 
use App\Models\Model; 

class Validation 
{
    public const RULE_REQUIRED = 'required';  
    private $model; 

    public function validate(Model $model, array $rules) 
    {    
        $this->model = $model;  
        $array = []; 
         foreach ($rules as $attribute => $rules) { 
            $value = $model->{$attribute};  // interpolazione dinamica, se $attribute = "name", diventa $this->name
            foreach ($rules as $rule) {

            if(!empty($_FILES[$attribute]) && $_FILES[$attribute]['error'] === 0)
            {
                continue; 
            }

              if ($rule === self::RULE_REQUIRED && !$value) {
                $array[$attribute] = $this->addError($attribute,$rule); 
              } 
              
              if(!empty($_FILES[$attribute]) && $_FILES[$attribute]['error'] !== 0) 
              {
                $array[$attribute] = $this->addError($attribute,$rule); 
              }
           
           }          
         }         
        return $array;   
    } 

    private function addError(string $attribute, string $rule) 
    {   
        return $this->getErrors($attribute,$rule)[$rule]; 
    } 

    private function getErrors(string $attribute, string $rule) 
    {
        if (isset($this->model->alias) && isset($this->model->alias[$attribute])) {
      
          $attribute = $this->model->alias[$attribute];
        }
        return [
            self::RULE_REQUIRED => $attribute.' is ' . $rule,
        ]; 
    }
}