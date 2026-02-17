<?php

namespace App\Traits; 

trait UCFirstSafe 
{
    public function ucfirstSafe(string|null $data) : string|null 
    {
        if (is_null($data)) {
            return null;   
        } 
        return ucfirst($data); 
    }
}

?>