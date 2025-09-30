<?php 

namespace App\Repositories; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL; 
use App\Core\Request; 

class UserRepository 
{
    private \PDO $pdo; 

    public function __construct() 
    {
       $this->pdo = Connection::connect(new MySql); 
    } 

    public function findEmail(Request $request) : array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute(['email' => $request->getBody()["email"]]);
        return $stmt->fetch();
    } 

    public function passwordVerify(string $password, string $passwordEncrypt) : bool
    {
       return password_verify($password,$passwordEncrypt);
    }
}