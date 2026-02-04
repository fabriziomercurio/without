<?php
declare(strict_types=1); 

namespace App\Core\Migration\Schema; 
use App\Core\Migration\Migrations; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Builders\TableBuilder; 

class Users extends Migrations
{         
    public function up(string $table) : bool 
    {             
        $obj = new TableBuilder; 
        $obj->table($table)
        ->addColumn('id', 'int AUTO_INCREMENT PRIMARY KEY', false)
        ->addColumn('firstname', 'varchar(255)', false)
        ->addColumn('lastname', 'varchar(255)', false)
        ->addColumn('email', 'varchar(255) UNIQUE', false)
        ->addColumn('password', 'varchar(255)', false)
        ->addColumn('age', 'varchar(255)')
        ->addColumn('city', 'varchar(255)')
        ->timestamps();
        $query = $obj->builder();
        return self::Conn()->prepare($query)->execute();
    } 

    public function down(string $table) : bool
    {    
        return $this->downTable($table);
    }
}