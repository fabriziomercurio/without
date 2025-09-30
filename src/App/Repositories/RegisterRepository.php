<?php 
declare(strict_types=1); 

namespace App\Repositories; 
use App\Core\Request;
use App\Models\User; 

class RegisterRepository 
{
    public function store(Request $request)
    {   
        $user = new User; 
        $user->firstname = ucfirst($request->getBody()['firstname']) ?? ''; 
        $user->lastname = ucfirst($request->getBody()['lastname']) ?? '';
        $user->email = $request->getBody()['email'] ?? '';
        $user->password = password_hash($request->getBody()['password'], PASSWORD_DEFAULT) ?? '';
        $user->age = $request->getBody()['age'] ?? '';
        $user->city = ucfirst($request->getBody()['city']) ?? '';
        $user->storeData($user, 'users'); 
        return $user; 
    }
}
