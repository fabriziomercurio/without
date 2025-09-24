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
        $user->firstname = $request->getBody()['firstname'] ?? ''; 
        $user->lastname = $request->getBody()['lastname'] ?? '';
        $user->email = $request->getBody()['email'] ?? '';
        $user->password = $request->getBody()['password'] ?? '';
        $user->age = $request->getBody()['age'] ?? '';
        $user->city = $request->getBody()['city'] ?? '';
        $user->storeData($user, 'users'); 
        return $user; 
    }
}
