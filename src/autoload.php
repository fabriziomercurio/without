<?php 

declare(strict_types=1); 

function autoloadClass(string $className) 
{
    $path = str_replace("\\","/", $className . ".php");   
    require_once $path;  
}

spl_autoload_register("autoloadClass"); 







?>