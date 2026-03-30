<?php 

declare(strict_types=1); 

function autoloadClass(string $className) 
{   
    require_once __DIR__ . '/vendor/autoload.php';
    $path = str_replace("\\","/", $className . ".php");   
    require_once $path;  
}

spl_autoload_register("autoloadClass"); 







?>