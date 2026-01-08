<?php 

namespace App\Controllers; 
use App\Core\Request; 

class FileUploadControllers 
{
    private Request $request; 

    public function __construct() 
    {
       $this->request = new Request; 
    } 

    public function store() 
    {   
      $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

      if (!is_dir($targetDir)) {
         mkdir($targetDir, 0755, true);
      }
      $file = $_FILES["image"];
      $targetFile =  $targetDir . uniqid() . "_" . basename($file["name"]);

      move_uploaded_file($_FILES["image"]["tmp_name"],$targetFile);
    }
}



?>