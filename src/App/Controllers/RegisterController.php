<?php
declare(strict_types=1); 

namespace App\Controllers;
use App\Controllers\Controller;  

class RegisterController extends Controller 
{
    public function show() 
    {
       $this->render->renderView('register-form'); 
    } 

    public function store() 
    {
        
    }
}

?>