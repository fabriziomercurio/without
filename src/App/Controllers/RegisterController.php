<?php
declare(strict_types=1); 

namespace App\Controllers;
use App\Controllers\Controller;  
use App\Services\RegisterService; 
use App\Core\Request; 
use App\Models\User; 

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
        $validate = $user->validate(); 
        var_dump($validate); 
        $this->render->renderView('register-form', $validate); 
        if (empty($validate)) {
             $this->registerService->store($request);
        }
        // if (!empty($validate)) {
        //     foreach ($validate as $key => $value) {
        //         echo $key . ' ' . $value . '</br>'; 
        //     }
        // }

        
 
        // $data = $this->registerService->store($request);
        
        // if ($data === true) { 
               
        //     return ['message' => 'record inserito correttamente', 'code' => 200];
        // }else {
        //     return ['message' => 'inserimento record non riuscito', 'code' => 500];
        // }
    }
}

?>