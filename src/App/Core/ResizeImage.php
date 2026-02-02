<?php 
declare(strict_types=1); 

namespace App\Core; 

class ResizeImage 
{
   private static array $filenames = []; 

   public static function hasFile(string $name) : bool 
   {       
      if (!isset($_FILES[$name])) { 
           return false; 
      } 
       
      $image = $_FILES[$name]; 

      if (!empty($image['tmp_name']) && $image['size'] > 0 && $image['error'] === UPLOAD_ERR_OK ) return true; 

      return false; 
   } 


   /**
    * $name string The name attribute specifies a name for an HTML element
    */
   public static function store(string $name, array $sizes, string $nameFolder) : array 
   { 
        
     if (count($sizes) !== 3) throw new \Exception('Image dimensions must be three'); 

     if(!preg_match('/^[a-zA-Z0-9_-]+$/', $nameFolder)) throw new \Exception('The folder name must not contain any symbols or numbers');

      $file = $_FILES[$name];
    
      $tmp = $file["tmp_name"];   

      $info = getimagesize($tmp); 
      if (!$info) throw new \Exception('Invalid image'); 

      list($width, $height, $type) = $info;

      switch ($type) { 
         case IMAGETYPE_JPEG:
             $src = imagecreatefromjpeg($tmp);
         break; 

         case IMAGETYPE_PNG:
            $src = imagecreatefrompng($tmp); 

         break; 
         default: 
            throw new \Exception('Unsupported image format');
      }

     $formats = ['max','medium','min'];  
       
     $unique = uniqid();       

     foreach ($sizes as $key => $size) { 

      $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/".strtolower($nameFolder)."/" . date("d-m-Y") . "/" . $formats[$key] . "/";  

      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);  

      if (!is_int($size)) throw new \Exception('values passed must to be integers'); 

     $filename = $targetDir . $unique . "_".$formats[$key]."_" . basename($file["name"]); 

      if ($width > $size) // $width > 1920 or 800 or 400
         {
            $newWidth = $size;
            $newHeight = intval(($height / $width) * $newWidth);

            //create a pattern that will filled 
            $dst = imagecreatetruecolor($newWidth, $newHeight); 

            if ($type === IMAGETYPE_PNG) {
              imagealphablending($dst, false); imagesavealpha($dst, true); 
              $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127); 
              imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
            }

            //it fills pattern 
            imagecopyresampled(
                  $dst, $src,
                  0, 0, 0, 0,
                  $newWidth, $newHeight,
                  $width, $height
            );

            //save image 
            if ($type === IMAGETYPE_PNG) {
               imagepng($dst, $filename, 9);
            }else{
               imagejpeg($dst, $filename, 90);
            }
           
            imagedestroy($dst);

            self::$filenames[] = $filename; 
           
         }else {
            self::$filenames[] = $filename;
            copy($tmp, $filename);
         } 
      }    
        imagedestroy($src);
        return self::$filenames;  
   }
}  

?> 




