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

        foreach ($lines as $line) {         
            // Ignore lines starting with "#/*^" and others, or don't have a value after "="
            preg_match('/\w+\=(.*)/',$line,$matches); 
              
            if(isset($matches[1])) 
            {
                putenv(trim($line)); 
            }
        }
    } 

    public static function getContent() 
    {
        $data = self::setContent();  
        self::$config['HOST'] = getenv('HOST'); 
        self::$config['USER'] = getenv('USER');
        self::$config['DATABASE'] = getenv('DATABASE');
        self::$config['PASSWORD'] = getenv('PASSWORD');
        self::$config['PUBLIC_KEY'] = getenv('PUBLIC_KEY');
        self::$config['PRIVATE_KEY'] = getenv('PRIVATE_KEY');
    } 

} 


?>