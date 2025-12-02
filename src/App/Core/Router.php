<?php 
declare(strict_types=1); 

namespace App\Core; 
use App\Controllers\ProductController; 
use App\Core\Request; 

class Router 
{   
    private array $routes = [];
    private Request $request; 

    public function __construct() 
    {
        $this->request = new Request; 
    }

    public function get(string $uri, callable|array $callback)  
    {
        $this->routes['GET'][$uri] = $callback; 
    } 

    public function post(string $uri, callable|array $callback)  
    {
        $this->routes['POST'][$uri] = $callback; 
    } 

    public function put(string $uri, callable|array $callback)  
    {
        $this->routes['PUT'][$uri] = $callback; 
    }

    public function delete(string $uri, callable|array $callback)  
    {
        $this->routes["DELETE"][$uri] = $callback; 
    }
    
    /**
     * @param array $routes 
     * 
     * Once the route (uri) has been passed, 
     * check the matches with the pattern 
     * through the preg_replace_callback 
     * and replace it like this, 
     * from "user/{userId:[1-9]+}/product/{product}"
     * to "user/([1-9]+)/product/([a-zA-Z0-9_-]+)" 
     * 
     * Call the preg_match method with the modified route 
     * and obtain the values ​​of the route 
     * (for example 123 or "bag"), 
     * with array_combine I create an array with parameters and values, 
     * finally launch the user_call_func_array
     */
    public function run() 
    {   
        $requestedRoute = trim($_SERVER['REQUEST_URI'], "/"); 

        $routes = $this->routes[$_SERVER['REQUEST_METHOD']]; 

        foreach ($routes as $uri => $callback) {            
           
            $routeRegex = preg_replace_callback('/{\w+(:([^}]+))?}/',function(array $matches)
            {   
                 
                return isset($matches[1]) ? '(' .$matches[2] . ')'  : '([a-zA-Z0-9_-]+)';
            },$uri); 

            $routeRegex = "@^". trim($routeRegex, "/") ."$@";         
          
            if (preg_match($routeRegex,$requestedRoute,$matches)) {              

                reset($matches); 
                unset($matches[key($matches)]); 
        
                $routeParamsValues = $matches;               
              
                $routeParamsNames = [];
                
                if (preg_match_all('/{(\w+)(:[^}]+)?}/', $uri,$match)) { 
                  
                    $routeParamsNames = $match[1];                     
                } 

                $routeParams = array_combine($routeParamsNames,$routeParamsValues);
                 
                       
                return $this->manageTypeCallback($callback,$routeParams); 
                
            }             
        } 

        exit('page not found');
    } 


    private function manageTypeCallback(callable|array $callback, array $routeParams) 
    {   
        if(is_callable($callback)) 
        {  
            return call_user_func_array($callback,$routeParams); 
        }

        if (is_array($callback) && $_SERVER['REQUEST_METHOD'] === 'GET') {            
            $callback[0] = new $callback[0];          
            return call_user_func_array($callback,$routeParams); 
        } 

        if (is_array($callback) && $_SERVER['REQUEST_METHOD'] === 'POST') { 
            $callback[0] = new $callback[0]; 
            return call_user_func($callback,$this->request); 
        }

        if (is_array($callback) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {                
            $callback[0] = new $callback[0];            
            return call_user_func_array($callback,$routeParams); 
        } 

        if (is_array($callback) && $_SERVER['REQUEST_METHOD'] === 'PUT') {   
            $callback[0] = new $callback[0];             
            return call_user_func_array($callback,[$routeParams['id'],$this->request]); 
        } 

    }

} //end class 
 



?> 