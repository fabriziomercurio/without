<?php
declare(strict_types=1); 

namespace App\Core\Migration; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Builders\TableBuilder; 
use App\Interfaces\Migration;
use App\Core\ENV; 
use App\Core\Logging\Logger; 

abstract class Migrations implements Migration  
{   
    protected static ?\PDO $pdo = null;

    public static function Conn() 
    {
       
       if (!self::$pdo) { self::$pdo = Connection::connect(new Mysql); } return self::$pdo;
        
    } 

    public static function upMigrationsTable() : bool
    {
        $table = new TableBuilder; 
        $table->table('migrations')
        ->addColumn('id','INT AUTO_INCREMENT PRIMARY KEY', false)
        ->addColumn('name','VARCHAR(255) UNIQUE')
        ->addColumn('batch','INT')
        ->timestamps();
        $query = $table->builder(); 
        $stmt = self::Conn()->prepare($query); 
        return $stmt->execute();   
    }

    public function downTable(string $table) : bool
    {    
        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ? AND table_name = ?"; 
        $data = self::Conn()->prepare($sql);  
        $fetch = $data->execute([ENV::$config["DATABASE"],$table]);         

        if (empty($data->fetch())) return false; 
        
        $sql = "DROP TABLE IF EXISTS " . strtolower($table);      
        $data = self::Conn()->prepare($sql); 
        $data->execute(); 
        return true;   
    } 

    public static function downAllTables() : bool 
    {
        try {
          self::checkIfDBIsNotEmpty(self::Conn()); 

          $sql = "SELECT table_name FROM information_schema.tables
          WHERE table_schema = ?"; 
          $data = self::Conn()->prepare($sql); 
          $res = $data->execute([ENV::$config['DATABASE']]); 
        
              $tables = $data->fetchAll(\PDO::FETCH_COLUMN);

              foreach ($tables as $table) {
                  self::Conn()->exec("SET FOREIGN_KEY_CHECKS = 0"); // disable foreign key controls                 
                  self::Conn()->exec('DROP TABLE IF EXISTS ' . $table); 
              }          
                                                  
              Logger::logMigration('migration.log','a+', '' , ' all migrations are down ','deleted_at');
        
          return true;
        } catch (\Throwable $th) {
            self::removeAnsiCharacter($th); 
        }
    }

    /**
     * checks if all tables are present in db 
     * and if the tables correspondents 
     * are in the Schema folder syncronized perfectly
     */
    public static function checkIfAllTablesExists() : array|false
    { 
       
        $migrations = self::scandirCustom([]); 
   
        $migrations[] = 'migrations';

        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = self::queryTable(ENV::$config["DATABASE"],$placeHolders, true); 
 
        $data = self::conn()->prepare($sql); 
        $params = array_merge([ENV::$config["DATABASE"]], $migrations);
        $data->execute($params); 

        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN); 

        if (count($countTables) !== count($migrations)) return false;     

        return $countTables; 
    } 

    private static function scandirCustom(array $migrations)
    {

    try {
        $schemaDir = __DIR__ . '/../Migration/Schema/';

    if (!is_dir($schemaDir)) {
        mkdir($schemaDir, 0755, true);
    }

    $migrationFiles = scandir($schemaDir);

    foreach ($migrationFiles as $migrate) {
        if ($migrate === '.' || $migrate === '..') continue;

        if (pathinfo($migrate, PATHINFO_EXTENSION) === 'php') {
            $className = substr($migrate, 0, strpos($migrate, '.php')); // es: 2025_10_30_160337_create_products_table

            // Estrai "products" dal nome file
            if (!preg_match('/^20\d{2}_\d{2}_\d{2}_\d{6}_create_(.+)_table$/', $className, $matches)) throw new \Exception("format name is invalid: `$migrate`" . PHP_EOL);
              
             $class = ucfirst($matches[1]); // es: Products 
             $fullClass = 'App\Core\Migration\Schema\\' . $class; // es: App\Core\Migration\Schema\Products

                $fullPath = $schemaDir . $migrate;

                if (!file_exists($fullPath)) throw new \Exception("File `$fullPath` not found" . PHP_EOL);
                    
                    require_once $fullPath;

                    if (!class_exists($fullClass, false)) throw new \Exception("Class `$class` not found in file `$migrate`" . PHP_EOL);
                      
                       $instance = new $fullClass;

                        if ($instance instanceof Migrations && method_exists($instance, 'up')) {
                            $migrations[] = strtolower($className);
                        }
            }
        }
         return $migrations;
    } catch (\Throwable $th) {
        self::removeAnsiCharacter($th);
    }
    
    }  

    private static function queryTable(string $db, string $placeHolders, bool $condition = false) : string
    {
        $addToQuery = ''; 

        if ($condition === true) {
            $addToQuery = "AND table_name IN ($placeHolders);";
        } 

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ?".$addToQuery;
        return $sql; 
    } 

    public static function insertInMigrationTable(string $table) : bool
    {  
        $sql = "SELECT name,batch FROM migrations WHERE name = ?"; 
        $data = self::conn()->prepare($sql);  
        $fetch = $data->execute([strtolower($table)]);    

        if (!$data->fetch()){
            $sql = "INSERT INTO migrations (name,batch) VALUES (?,?);"; 
            $data = self::conn()->prepare($sql);  
            $fetch = $data->execute([strtolower($table),1]); 
            Logger::logMigration('migration.log','a+', $table, ' migrate with success');           
            return true;  
        }else { 
            $data = self::conn()->prepare($sql);  
            $fetch = $data->execute([strtolower($table)]); 
            $fetch = $data->fetchAll();            
            $sql = "UPDATE migrations SET batch = ? WHERE name = ?"; 
            foreach ($fetch as $value) {
                $batch = $value['batch'] + 1;
                $data = self::conn()->prepare($sql);  
                $fetch = $data->execute([$batch, strtolower($table)]); 
            }
            Logger::logMigration('migration.log','a+', $table, ' update with success', 'updated_at');
            return true;
        }

        return false; 
    } 

    public static function deleteInMigrationTable(string $table) 
    { 
        $sql = "SELECT name FROM migrations WHERE name = ?;";

        $data = self::conn()->prepare($sql);  
        $fetch = $data->execute([strtolower($table)]); 

        if ($data->fetch()){
         $sql = "DELETE FROM migrations WHERE name = ?"; 
 
         $data = self::conn()->prepare($sql);  
         $fetch = $data->execute([strtolower($table)]); 
         Logger::logMigration('migration.log','a+', $table, ' delete with success', 'delete_at');
         return true; 
        }else {
            return false; 
        }
    } 

    public static function createMigration(string $string) 
    {  
        try {

            if (strpos($string, 'create_') !== 0 or str_ends_with($string,'_table') === false){
               throw new \Exception("syntax for this argument is not correct: \033[1m\033[3m".$string."\033[0m\n 
               it's correct : \033[1mcreate_\033[3mtablename\033[0m\033[1m_table\033[0m\n");
            }  

            if (!is_dir(__DIR__ . '/../Migration/Schema/')) {
                mkdir(__DIR__ . '/../Migration/Schema/', 0755, true);
            }

            $migrations = scandir(__DIR__ . '/../Migration/Schema/');  
           
            foreach ($migrations as $migration) {
              if($migration === '.' || $migration === '..') continue; 

              $migrationName = current(explode(".php",preg_replace('/^(20\d{2}_\d{2}_\d{2}_\d{6})_/', '', $migration))); 
      
             if ($string === $migrationName) throw new \Exception('migration is already exists'.PHP_EOL) ;  
          
            } 

           $migrationName = preg_replace('/^(20\d{2}_\d{2}_\d{2}_\d{6})_/', '', $string);
       
            $stringFile = date('Y_m_d_His_').$migrationName.'.php'; 

            if(empty(preg_match('/^(20)\d{2}_\d{2}_\d{2}_\d{6}_[a-zA-Z0-9_]+\.php$/', $stringFile, $matches))) throw new \Exception('format is invalid' . PHP_EOL); 
           
            $nameClass = preg_replace(['/^create_/', '/_table$/'], '', $string);
            $nameClass = ucfirst($nameClass); 

            $fullPath = __DIR__ . '/../Migration/Templates/migration_template.php';   
            
            if(!file_exists($fullPath))  throw new \Exception("File `$fullPath` not found".PHP_EOL); 

            require_once $fullPath; 
            
            $content = sprintf($template, $nameClass);
            file_put_contents(dirname(__DIR__) . '/Migration/Schema/'.$stringFile, $content); 

            echo json_encode("migration {$stringFile} created with success!");

        } catch (\Throwable $th) {
            self::removeAnsiCharacter($th);
        }
    } 

    public static function formatFileNameMigration(string $migration) : array 
   { 
      if(file_exists($migration)) throw new \Exception("The file `$migration` does not exist" . PHP_EOL);
      
      if (!str_contains($migration, '.php')) throw new \Exception("this file migration `$migration` does not contains .php at the end".PHP_EOL); 
       
      $fileWithoutExtension = substr($migration, 0, strpos($migration, '.php'));
      
      if (!preg_match('/^20\d{2}_\d{2}_\d{2}_\d{6}_create_(.+)_table$/', $fileWithoutExtension, $matches)) throw new \Exception("format name is invalid: `$migration`" . PHP_EOL); 

      return $matches; 
   } 

   public static function checkIfDBIsNotEmpty(\PDO $pdo) : void 
   {
        $sql = "SELECT COUNT(*) AS numero_tabelle
        FROM information_schema.tables
        WHERE table_schema = ?"; 
        $data = self::conn()->prepare($sql); 
        $data->execute([ENV::$config['DATABASE']]); 

        $numberTables = $data->fetchColumn(); 

        if ((int)$numberTables === 0) {
            Logger::logMigration('migration.log','a+', '' , ' impossible delete all migration because db is already empty ','',false);
            throw new \Exception('impossible delete all migrations because db is already empty'.PHP_EOL);
               
        }
   } 

   public static function removeAnsiCharacter($th) 
   {  
      $clean = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '',$th->getMessage().' at line '.$th->getLine());
      echo json_encode(trim($clean)); 
   }

} 

?>