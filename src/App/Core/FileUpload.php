<?php 
declare(strict_types=1); 

namespace App\Core; 

class FileUpload 
{
   public static function store(string $name) 
   {   
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

     if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
     }
     $file = $_FILES[$name];
     $targetFile =  $targetDir . uniqid() . "_" . basename($file["name"]);

     move_uploaded_file($_FILES[$name]["tmp_name"],$targetFile);
   }
} 


?>