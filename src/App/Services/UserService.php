<?php 
declare(strict_types=1); 

namespace App\Services; 
use App\Repositories\UserRepository; 
use App\Core\Request; 

class UserService 
{
    private UserRepository $user; 

    public function __construct() 
    {
       $this->user = new UserRepository; 
    } 

    public function findEmail(Request $request) : array|false
    {
       return $this->user->findEmail($request); 
    } 

    public function passwordVerify(string $password, string $passwordEncrypt) : bool
    {
       return $this->user->passwordVerify($password,$passwordEncrypt); 
    }
}