<?php 
declare(strict_types=1);

namespace App\Services; 
use App\Repositories\RegisterRepository;
use App\Core\Request;

class RegisterService 
{      
    public function __construct(private RegisterRepository $registerRepository = new RegisterRepository) 
    {} 

    public function store(Request $request) 
    {
       return $this->registerRepository->store($request); 
    }
}