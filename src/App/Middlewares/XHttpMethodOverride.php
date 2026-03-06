<?php 

namespace App\Middlewares; 

class XHttpMethodOverride 
{
  /**
   * This middleware must to be used only for testing 
   * when you want lunch a rest client request in form-data 
   * with PUT method
   * example in rest client: 
   * POST http://localhost:8000/api/testing/{id}
   * X-HTTP-Method-Override: PUT
   */
  public static function handle() 
  { 
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ) { 
       $_SERVER['REQUEST_METHOD'] = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']); 
    }
  }
}

?>