<?php 
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Render; 

abstract class Controller 
{     
    public function __construct(protected Render $render = new Render) 
    {}
}





?>