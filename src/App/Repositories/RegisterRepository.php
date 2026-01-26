<?php 
declare(strict_types=1); 

namespace App\Repositories; 
use App\Core\Request;
use App\Models\User; 
use App\Traits\UCFirstSafe; 

class RegisterRepository 
{
    use UCFirstSafe; 
    
    public function store(Request $request)
    {   
        $user = new User; 
        $user->firstname = ucfirst($request->getBody()['firstname']); 
        $user->lastname = ucfirst($request->getBody()['lastname']);
        $user->email = $request->getBody()['email'];
        $user->password = password_hash($request->getBody()['password'], PASSWORD_DEFAULT);
        $user->age = $request->getBody()['age'] ?? null;
        $user->city = $this->ucfirstSafe($request->getBody()['city'] ?? null);
        return $user->store(); 
    }
}
