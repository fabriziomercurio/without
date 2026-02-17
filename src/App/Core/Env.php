<?php 
declare(strict_types=1); 

namespace App\Core; 

class Env 
{  
    public static Array $config = []; 

    private static function fileGetContent(string $file) : string
    {
        return file_get_contents($file); 
    } 

    private static function setContent() 
    {
        $file = self::fileGetContent(".env"); 

        $lines = explode("\n",$file); 

        $data = []; 

        foreach ($lines as $line) {         
            // Ignore lines starting with "#/*^" and others, or don't have a value after "="
            preg_match('/\w+\=(.*)/',$line,$matches); 
              
            if(isset($matches[1])) 
            {
                putenv(trim($line)); 
                [$key, $value] = explode('=', $line, 2); 
                $data[$key] = trim($value);
            }
        }
        return $data; 
    } 

    public static function getContent() 
    {
        self::$config = self::setContent();
    } 
} 


?>