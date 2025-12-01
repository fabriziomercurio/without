<?php 

namespace App\Controllers; 
use App\Core\Request; 
use App\Core\Response;
use App\Core\Tokens\GetToken; 
use App\Core\Tokens\Jwt;
use App\Services\UserService;
use App\Core\Env;  

class LoginController 
{   
    private GetToken $token;

    public function __construct(private UserService $user = new UserService
    ){
        $this->token = new GetToken(new Jwt(ENV::$config['PRIVATE_KEY']));
    }  

    public function doLogin(Request $request) 
    {
        try {

        $user = $this->user->findEmail($request); 
      
        if(is_array($user) && $this->user->passwordVerify($request->getBody()["password"], $user["password"]))
        {            
            $payload = [
               "user_id" => $user["id"], 
               "user_name" => $user["firstname"], 
               "exp" => time() + 3600 
            ]; 

            $data = $this->token->create($payload);     
            
            if(!$this->token->validate($data,ENV::$config['PUBLIC_KEY']))
            {
                throw new \Exception("Token non valido");
            }

            if (!$this->token->isExpired($payload)) {
                throw new \Exception("Token scaduto");
            }
           
            echo Response::success("ti sei loggato correttamente",['name' =>  $user["firstname"]], 200 , $data); 
          }else { 
            echo Response::error("credenziali non valide");
          }
        } catch (\Throwable $th) {
           echo Response::error($th->getMessage());
        }
    }
} 


?>

