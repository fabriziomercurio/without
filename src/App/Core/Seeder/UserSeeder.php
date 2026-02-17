<?php
declare(strict_types=1); 

namespace App\Core\Seeder; 
use App\Core\Seeder\Seeder; 
use App\Models\User; 

class UserSeeder extends Seeder
{         
    public function run() 
    { 
        try {
            $data = new User; 
            $data->firstname = 'Fabrizio'; 
            $data->lastname = 'Mercurio';
            $data->email = 'test@test.it'; 
            $data->password = password_hash('T4bUcchiolo!', PASSWORD_DEFAULT);
            $data->age = 39;
            $data->city = 'Torino'; 
            $data->store();        
            echo json_encode('Seeder run!!'.PHP_EOL); 
        } catch (\Throwable $th) {
            echo json_encode($th->getMessage()); 
        }
    }
} 
