<?php
declare(strict_types=1); 

namespace App\Core; 

class Render 
{  
   public function renderView(string $viewName, array $params = []) 
   {       
      $main = $this->layoutMainContent(); 
      $view = $this->layoutOnlyView($viewName, $params); 
      echo str_replace("{{content}}",$view,$main);
   } 

   private function layoutMainContent() 
   {  
      ob_start();
      require_once 'layouts/main/main.php'; 
      return ob_get_clean();
   } 

   private function layoutOnlyView(string $viewName, array $params) 
   {   
        foreach ($params as $key => $value) {
            $$key = $value;   
        } 
        
        ob_start();
        require_once 'layouts/parts/'.$viewName.'.php'; 
        return ob_get_clean(); 
   }


}






?>