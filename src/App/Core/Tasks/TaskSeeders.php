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

      if(!preg_match('/^[A-Za-z]+$/', $seederName, $matches) or !str_ends_with($seederName,'Seeder')) throw new \Exception("syntax for this argument is not correct: \033[1m\033[3m".$seederName."\033[0m\n 
               it's correct : \033[\033[3mNameSeeder\033[0m\n");

        $seeders = scandir(__DIR__ . '/../Seeder/'); 

        $validSeeders = array_filter($seeders, function($seeder) {  
           return $seeder !== '.' && $seeder !== '..' && $seeder !== 'Templates';
        }); 

        if (empty($validSeeders)) { 
 
            $path = __DIR__ . '/../Seeder/Templates/seeder_template.php'; 
            if (!file_exists($path)) throw new \Exception("template file not exists in this path `$path`" . PHP_EOL);  
            $this->createSeederFile($seederName);  
           
        } else {
            foreach ($validSeeders as $seeder) { 
 
                $className = substr($seeder, 0, strpos($seeder, '.php'));
                if($className === $seederName) throw new \Exception("file `$seeder` already exists".PHP_EOL);  
                 
            } 
            $this->createSeederFile($seederName);
        }

      } catch (\Exception $th) {
         $this->removeANSICharacter($th); 
      }
   } 

   private function createSeederFile(string $seederName) : void 
   {
        $templatePath = __DIR__ . '/../Seeder/Templates/seeder_template.php';    
        if (!file_exists($templatePath)) throw new \Exception("template file not exists in this path `$templatePath`" . PHP_EOL);
      
        $template = require $templatePath; 
     
        if (!is_string($template)) { throw new \Exception("template file `$templatePath` must return a string" . PHP_EOL); }

        $content = sprintf($template, $seederName); 
        $file = dirname(__DIR__) . '/Seeder/'.$seederName.".php"; 
        file_put_contents($file, $content);
        
        chmod($file,0777);
        throw new \Exception("file `$seederName` created!".PHP_EOL);  
   }

   public function runSeeder(string $seederName) 
   {      
      try { 

       if(!preg_match('/^[A-Za-z]+$/', $seederName, $matches) or !str_ends_with($seederName,'Seeder')) throw new \Exception("syntax for this argument is not correct: \033[1m\033[3m".$seederName."\033[0m\n 
               it's correct : \033[\033[3mNameSeeder\033[0m\n");

       $seeders = scandir(__DIR__ . '/../Seeder/'); 

        $validSeeders = array_filter($seeders, function($seeder) {  
       
           return $seeder !== '.' && $seeder !== '..' && $seeder !== 'Templates';
        }); 
      
        if (empty($validSeeders)) { 
           throw new \Exception('Seeder folder does\'t contains Seeder files ');
        } 

        $seederFile = __DIR__ . '/../Seeder/' . $seederName . '.php'; 
        if (!file_exists($seederFile)) throw new \Exception("seeder file `$seederName.php` does not exist" . PHP_EOL); 

        foreach ($validSeeders as $seeder) { 
         
           if($seeder === '.' || $seeder === '..' || $seeder === 'Templates') continue; 

           $className = substr($seeder, 0, strpos($seeder, '.php')); 

           if ($className === $seederName) {
              require_once __DIR__ .'/../Seeder/'.$seederName.'.php';
              $fullClass = "App\\Core\\Seeder\\".$seederName;
              $obj = new $fullClass; 
              $obj->run(); 
           }

        }

      } catch (\Exception $th) {
        $this->removeANSICharacter($th); 
      }
   } 

   public function removeANSICharacter($th) 
   {
      $clean = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $th->getMessage());
      echo json_encode(trim($clean));
   }
}