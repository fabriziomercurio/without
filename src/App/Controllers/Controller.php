<?php 
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Render; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

abstract class Controller 
{     
    protected \PDO $pdo; 

    public function __construct(protected Render $render = new Render) 
    {
        $this->pdo = Connection::connect(new Mysql);
    }
}


?>