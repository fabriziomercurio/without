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
        $errors = $user->validation($request->getBody()); 

        if (!empty($errors)) {
            echo json_encode($errors);
            exit;
        } 

        $user = $this->registerService->store($request);
        Response::success('Utente registrato con successo',$user);
    }
}
?>
