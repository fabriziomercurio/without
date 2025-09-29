<?php 

namespace App\Interfaces; 

interface Token 
{
    public function create(array $array); 
    public function validate(string $token, string $publicKeyPath);
}

?> 