<?php
declare(strict_types=1); 

namespace App\Core\Migration\Schema; 
use App\Core\Migration\Migrations; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Builders\TableBuilder; 

class Products extends Migrations
{         
    public function up(string $table) : bool 
    {             
        $obj = new TableBuilder; 
        $obj->table($table)
        ->addColumn('id', 'int AUTO_INCREMENT PRIMARY KEY', false)
        ->addColumn('name', 'varchar(255)')
        ->addColumn('price', 'DECIMAL(10,2)')
        ->addColumn('category', 'varchar(255)')
        ->addColumn('description', 'varchar(255)')
        ->addColumn('available', 'TINYINT(1) NOT NULL DEFAULT 0')
        ->addColumn('brand', 'varchar(255)')
        ->addColumn('code', 'varchar(255)') 
        ->addColumn('weight', 'float')
        ->timestamps();
        $query = $obj->builder();
        return $this->pdo->prepare($query)->execute();
    } 

    public function down(string $table) : bool
    {    
        return $this->downTable($table);
    }
}