<?php 

namespace App\Middlewares; 

class Cors 
{
  public static function handle() 
  { 
    ob_start();
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      header("Access-Control-Max-Age: 3600"); 
      exit; 
    }
    return true;
  }
}

?>