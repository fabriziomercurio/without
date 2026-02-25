<?php 
declare(strict_types=1); 

namespace App\Repositories; 
use App\Models\Multimedia; 
use App\Core\Request; 

class MultimediaRepository 
{
    public function store(Request $request) : mixed 
    {   
        $data = new Multimedia; 
        $data->multi_name = $request->extra["fileimage"] ?? null; 
        return $data->store(); 
    } 

    public function edit(int $id) : bool | array
    {
        return Multimedia::edit($id); 
    } 

    public function update(int $id, Request $request) : bool 
    { 
       $data = new Multimedia; 
       $data->multi_name = $request->extra["filename"] ?? null; 
       return $data->update($id,$request);  
    } 

    public function delete(int $id) 
    {
        Multimedia::delete($id); 
    }
}

?>