<?php 

namespace App\Interfaces; 

interface Migration 
{
    public function up(string $table); 
    public function down(string $table);  
}

?> 