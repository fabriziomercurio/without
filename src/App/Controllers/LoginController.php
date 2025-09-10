<?php 

namespace App\Controllers; 
use App\Core\Request; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL; 

class LoginController 
{   
    private \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql);
    } 

    public function doLogin(Request $request) 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=:email AND PASSWORD = :password");
        $stmt->execute(['email' => $request->getBody()["email"], 'password' => $request->getBody()["password"]]); 
        $user = $stmt->fetch();
        if(is_array($user) && $user !== false) 
        {
            echo "ti sei loggato correttamente"; 
        }else {
            echo "credenziali non valide"; 
        }
    }
} 


?>

