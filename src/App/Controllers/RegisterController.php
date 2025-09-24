<?php
declare(strict_types=1); 

namespace App\Controllers;
use App\Controllers\Controller;  
use App\Services\RegisterService; 
use App\Core\Request; 
use App\Models\User; 
use App\Core\Response;

class RegisterController extends Controller 
{   
    public function __construct(private RegisterService $registerService = new RegisterService) 
    {
        parent::__construct();
    } 

    public function show() 
    {
       $this->render->renderView('register-form'); 
    } 

    public function store(Request $request) 
    {   
        $user = new User; 
        $user->loadData($request->getBody());       
        $user = $this->registerService->store($request);
        Response::success('Utente registrato con successo',$user);
        //$validate = $user->validate();  
    }
}
?>
