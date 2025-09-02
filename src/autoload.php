<?php 

declare(strict_types=1); 

function autoloadClass(string $className) 
{
    $path = str_replace("\\","/", $className . ".php");   
    echo $path . PHP_EOL; 
    require_once $path;  
}

spl_autoload_register("autoloadClass"); 







?>