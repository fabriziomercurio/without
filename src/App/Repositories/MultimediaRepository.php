<?php 
declare(strict_types=1); 

namespace App\Repositories; 
use App\Models\Multimedia; 
use App\Core\Request; 

class MultimediaRepository 
{
    public function store(Request $request) : string|false  
    {
        $data = new Multimedia; 
        $data->name = $request->getBody()['name'] ?? null; 
        return $data->store(); 
    } 
}

?>