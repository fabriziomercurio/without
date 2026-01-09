<?php
declare(strict_types=1); 

namespace App\Core\Builders; 

class TableBuilder 
{
    public string $table; 
    public array $columns = []; 

    public function table(string $table) : self 
    {
       $this->table = strtolower($table); 
       return $this; 
    } 
  
    public function addColumn(string $name, string $type, bool $nullable = true) : self
    {
        $null = ($nullable == true) ? "NULL" : "NOT NULL";
        $this->columns[] = "$name $type $null";  
        return $this;
    } 

    public function foreignKey(string $column, string $refTable, string $refColumn = 'id') : self
    {
        $this->columns[] = "FOREIGN KEY ($column) REFERENCES $refTable($refColumn)"; 
        return $this;
    } 

    public function timestamps() : self
    {        
        $this->columns[] = "created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP";  
        $this->columns[] = "updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function builder() : string
    {
        $columnSQL =  implode(", ", $this->columns); 
        $query = "CREATE TABLE IF NOT EXISTS {$this->table} ({$columnSQL})"; 
        return $query;  
    }
}