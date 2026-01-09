<?php 

namespace App\Services; 
use App\Repositories\MultimediaRepository;
use App\Core\Request;

class MultimediaService 
{
    public function __construct(private MultimediaRepository $multimediaRepository = new MultimediaRepository) 
    {}

    public function store(Request $request) : string|false
    {
       return $this->multimediaRepository->store($request); 
    }
}

?>