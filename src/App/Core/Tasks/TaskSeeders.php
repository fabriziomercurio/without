<?php
declare(strict_types=1); 

namespace App\Core\Tasks; 
use App\Core\Migration\Migrations; 
use App\Core\Migration\Schema; 
use App\Core\Connections\MySQL;
use App\Core\Connections\Connection; 

class TaskSeeders
{ 
   /* /^[A-Za-z]+$/ excludes numbers and special characters */
   public function createSeeder(string $seederName)
   {   
      try { 

        if(!preg_match('/^[A-Za-z]+$/', $seederName, $matches) or !str_ends_with($seederName,'Seeder')) exit("syntax for this argument is not correct: \033[1m\033[3m".$seederName."\033[0m\n 
               it's correct : \033[\033[3mNameSeeder\033[0m\n");

        $seeders = scandir(__DIR__ . '/../Seeder/'); 

        $validSeeders = array_filter($seeders, function($seeder) {  
           return $seeder !== '.' && $seeder !== '..' && $seeder !== 'Templates';
        }); 

        if (empty($validSeeders)) { 
 
            $path = __DIR__ . '/../Seeder/Templates/seeder_template.php'; 
            if (!file_exists($path)) exit("template file not exists in this path `$path`" . PHP_EOL);  
            $this->createSeederFile($seederName);  
           
        } else {
            foreach ($validSeeders as $seeder) { 
 
                $className = substr($seeder, 0, strpos($seeder, '.php'));
                if($className === $seederName) exit("file `$seeder` already exists".PHP_EOL); 
                $this->createSeederFile($seederName); 
            }
        }

      } catch (\Throwable $th) {
        exit('impossible to send a specific seeder ' . $th->getMessage());
      }
   } 

   private function createSeederFile(string $seederName) : void 
   {
        require_once __DIR__ . '/../Seeder/Templates/seeder_template.php';
        $content = sprintf($template, $seederName); 
        file_put_contents(dirname(__DIR__) . '/Seeder/'.$seederName.".php", $content);
   }

   public function runSeeder(string $seederName) 
   {      
      try { 

       if(!preg_match('/^[A-Za-z]+$/', $seederName, $matches) or !str_ends_with($seederName,'Seeder')) exit("syntax for this argument is not correct: \033[1m\033[3m".$seederName."\033[0m\n 
               it's correct : \033[\033[3mNameSeeder\033[0m\n");

       $seeders = scandir(__DIR__ . '/../Seeder/'); 

        $validSeeders = array_filter($seeders, function($seeder) {  
           return $seeder !== '.' && $seeder !== '..' && $seeder !== 'Templates';
        }); 
        
        if (empty($validSeeders)) {
           exit('Seeder folder does\'t contains Seeder files ' . $th->getMessage());
        } 

        foreach ($validSeeders as $seeder) {
           if($seeder === '.' && $seeder === '..' && $seeder === 'Templates') continue; 

           $className = substr($seeder, 0, strpos($seeder, '.php')); 

           if ($className === $seederName) {
              require_once __DIR__ .'/../Seeder/'.$seederName.'.php';
              $fullClass = "App\\Core\\Seeder\\".$seederName;
              $obj = new $fullClass; 
              $obj->run(); 
           }

        }

      } catch (\Throwable $th) {
        exit("impossible to runs this specific seeder `$seederName` ". $th->getMessage());
      }
   }
}