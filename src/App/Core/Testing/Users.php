<?php
declare(strict_types=1); 

namespace App\Core\Testing; 
use App\Core\Migrations; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

class Users extends Migrations
{         
    function up(string $table) 
    {   
        $sql = "CREATE TABLE IF NOT EXISTS ".strtolower($table)." (
        id int AUTO_INCREMENT PRIMARY KEY NOT NULL,
        name varchar(255), 
        surname varchar(255), 
        age varchar(255), 
        city varchar(255), 
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );"; 
        $data = $this->pdo->prepare($sql); 
        $data->execute();
    } 

    public function down(string $table) 
    {    
        $pdo = Connection::connect(new Mysql);
        $sql = "DROP TABLE IF EXISTS ".strtolower($table); 
        $data = $this->pdo->prepare($sql); 
        $data->execute();
    }
}