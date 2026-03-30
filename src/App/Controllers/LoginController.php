<?php 

namespace App\Controllers; 
use App\Core\Request; 
use App\Core\Response;
use App\Core\Tokens\GetToken; 
use App\Core\Tokens\Jwt;
use App\Services\UserService;
use App\Core\Env; 
use App\Core\Tokens\BlackList\BlackListTokens as BlackList;  
use App\Core\Tokens\CsrfGenerate;  

class LoginController 
{   
    private GetToken $token; 
    private CsrfGenerate $csrf;

    public function __construct(private UserService $user = new UserService
    ){
        $this->token = new GetToken(new Jwt(ENV::$config['PRIVATE_KEY']));
        $this->csrf = new CsrfGenerate; 
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
               "exp" => time() + 3600, 
               "jti" => bin2hex(random_bytes(16)) 
            ]; 

            $data = $this->token->create($payload);     
           
            Response::success("logging with success",['name' =>  $user["firstname"]], 200 , $data, $this->csrf->generate()); 
          }else { 
            Response::error("credentials are not valid");
          }
        } catch (\Throwable $th) {
           Response::error($th->getMessage());
        }
    } 

    public function doLogout() 
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null; 
 
        if (!$auth || $auth === "Bearer null") {
            http_response_code(401);
            echo json_encode(["error" => "Token missed"]);
            exit;
        }

        $token = str_replace('Bearer ', '', $auth);
        $token = trim($token, "\""); 

        try {     
            $jwt = new Jwt('private.key');
            $payload = $jwt->decodePayload($token);  

            $blacklist = new BlackList;    
            $blacklist->add($payload["jti"],$payload["exp"]); 
   
            Response::success("logout works!"); 

        } catch (\Throwable $th) {
            Response::error($th->getMessage());
        }
    }
} 


?>

