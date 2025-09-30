<?php 

namespace App\Controllers; 
use App\Core\Request; 
use App\Core\Tokens\GetToken; 
use App\Core\Tokens\Jwt;
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Env;  

class LoginController 
{   
    private \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql);
    } 

    public function doLogin(Request $request) 
    {
        try {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=:email AND PASSWORD = :password");
        $stmt->execute(['email' => $request->getBody()["email"], 'password' => $request->getBody()["password"]]); 
        $user = $stmt->fetch();
        if(is_array($user) && $user !== false) 
        {             
            $token = new GetToken(new Jwt(ENV::$config['PRIVATE_KEY']));

            $payload = [
               "user_id" => $user["id"], 
               "user_name" => $user["firstname"], 
               "exp" => time() + 3600 
            ]; 

            $data = $token->create($payload);     
            
            $token->validate($data,ENV::$config['PUBLIC_KEY']);  

            if ($token->isExpired($payload)) {
                throw new \Exception("Token scaduto");
            }
           
            header('Content-Type: application/json');
            echo json_encode(['message' => "ti sei loggato correttamente", "token" => $data]); 
        }else { 
            header('Content-Type: application/json');
            echo json_encode(['error' => "credenziali non valide"]); 
        }
        } catch (\Throwable $th) {
            header('Content-Type: application/json');
             echo json_encode([
            'error' => $th->getMessage()
          ]);
        }
    }
} 


?>

