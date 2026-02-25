<?php 

namespace App\Services; 
use App\Repositories\MultimediaRepository;
use App\Core\Request;

class MultimediaService 
{
    public function __construct(private ?MultimediaRepository $multimediaRepository = null) 
    {
       $this->multimediaRepository = $multimediaRepository ?? new MultimediaRepository; 
     }

    public function store(Request $request) : string|false
    {
       return $this->multimediaRepository->store($request); 
    } 

    public function edit(int $id) : bool | array
    {
       return $this->multimediaRepository->edit($id);  
    } 

    public function update(int $id, Request $request) : bool 
    {
       return $this->multimediaRepository->update($id,$request); 
    } 

    public function delete(int $id)  
    {
       return $this->multimediaRepository->delete($id); 
    }
}

?>