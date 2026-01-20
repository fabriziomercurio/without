<?php 
declare(strict_types=1); 

namespace App\Core; 

class ResizeImage 
{
   /**
    * $name string The name attribute specifies a name for an HTML element
    */
   public static function store(string $name, array $sizes) 
   { 
     
     if (count($sizes) !== 3) exit('Image dimensions must be three'); 
       
     $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/"; 

     $file = $_FILES[$name];

     if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      
    
      $tmp = $_FILES[$name]["tmp_name"];   

      list($width, $height,$type) = getimagesize($tmp); 

      switch ($type) { 
         case IMAGETYPE_JPEG:
             $src = imagecreatefromjpeg($tmp);
         break; 

         case IMAGETYPE_PNG:
            $src = imagecreatefrompng($tmp); 

         break; 
         default: 
            exit('Unsupported image format');
      }


      $formats = ['max','medium','min'];  
       
      $unique = uniqid();       

      foreach ($sizes as $key => $size) { 

      $filename = $targetDir . $unique . "_".$formats[$key]."_" . basename($file["name"]); 

      if ($width > $size) // $width > 1920 o 800 o 400
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

            // //save image 
            // $filename = $targetDir . $unique . "_".$formats[$key]."_" . basename($file["name"]); 
            if ($type === IMAGETYPE_PNG) {
               imagepng($dst, $filename);
            }else{
               imagejpeg($dst, $filename, 90);
            }

            imagedestroy($src);
            imagedestroy($dst);
         }else {
            move_uploaded_file($tmp, $filename);
         }
      }    
   }
}  

?> 




