<?php 
declare(strict_types=1); 

namespace App\Core\Migration\Schema; 
use App\Core\Migration\Migrations; 
use App\Core\Builders\TableBuilder; 

class Products extends Migrations
{
    function up(string $products) 
    {   
        $query = new TableBuilder; 
        $query->table($products) 
        ->addColumn('id','int AUTO_INCREMENT PRIMARY KEY',false)
        ->addColumn('firstname','varchar(255)')
        ->addColumn('lastname','varchar(255)')
        ->addColumn('email','varchar(255)')
        ->addColumn('password','varchar(255)')
        ->addColumn('age','varchar(255)')
        ->addColumn('city','varchar(255)')
        ->timestamps(); 
        $sql = $query->builder();
        $this->pdo->prepare($sql)->execute(); 
    }

    public function down(string $table) 
    {
        $this->downTable($table);
    }
}