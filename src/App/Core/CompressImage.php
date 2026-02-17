<?php 
declare(strict_types=1); 

namespace App\Core; 

class CompressImage 
{ 
    public static function run(array $filenames, string $mime) : void  
    {    
       self::checkIfArrayEmpty($filenames);
       self::checkMime($mime); 
        
        foreach ($filenames as $source) { 
            if ($mime == 'image/png') {       
               self::compressPNG($source); 
            }elseif ($mime == 'image/jpeg') {          
               self::compressJPEG($source); 
            } 
          }
    }

    private static function checkIfArrayEmpty(array $filenames) 
    {
      if(empty($filenames)) throw new \Exception("Filenames cannot be empty");
    } 

    private static function checkMime(string $mime) 
    {
      if ($mime !== 'image/png' && $mime !== 'image/jpeg') throw new Exception("Unsupported MIME type");
    }

    private static function compressPNG($source)
    {
       $image = imagecreatefrompng($source);
       
       // Maintain transparency
       imagealphablending($image, false); 
       imagesavealpha($image, true);
       
       imagepng($image, $source, 9);
       imagedestroy($image);
    } 

    private static function compressJPEG($source)
    {
       $image = imagecreatefromjpeg($source);
       imagejpeg($image, $source, 80);
       imagedestroy($image);
    }

}