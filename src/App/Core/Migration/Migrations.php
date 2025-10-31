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
    protected \PDO $pdo; 

    public function __construct() 
    {
        $this->pdo = Connection::connect(new Mysql); 
    }

    public static function upMigrationsTable() : \PDO 
    {
        $pdo = Connection::connect(new Mysql); 
        $table = new TableBuilder; 
        $table->table('migrations')
        ->addColumn('id','INT AUTO_INCREMENT PRIMARY KEY', false)
        ->addColumn('name','VARCHAR(255) UNIQUE')
        ->addColumn('batch','INT')
        ->timestamps();
        $query = $table->builder(); 
        $pdo->prepare($query)->execute(); 
        return $pdo;  
    }

    public function downTable(string $table) : bool
    {    
        ENV::getContent();  

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ? AND table_name = ?"; 
        $data = $this->pdo->prepare($sql);  
        $fetch = $data->execute([ENV::$config["DATABASE"],$table]);         

        if (empty($data->fetch())) return false; 
        
        $sql = "DROP TABLE IF EXISTS " . strtolower($table);      
        $data = $this->pdo->prepare($sql); 
        $data->execute(); 
        return true;   
    } 

    public static function downAllTables() 
    {
        $pdo = Connection::connect(new MySQL); 
        ENV::getContent(); 

        $sql = "SELECT table_name FROM information_schema.tables
        WHERE table_schema = ?"; 
        $data = $pdo->prepare($sql); 
        $res = $data->execute([ENV::$config['DATABASE']]); 
        if ($res) {
            foreach($data->fetchAll(\PDO::FETCH_COLUMN) as $table) 
            {   
                $pdo->exec('DROP TABLE IF EXISTS ' . $table);                         
            } 
            Logger::logMigration('migration.log','a+', '' , ' all migrations are down ','deleted_at');
            exit('delete all migrations'); 
        } 
        Logger::logMigration('migration.log','a+', '' , ' db not found ','deleted_at',false);
        exit('db not found');         
    }

    /**
     * checks if all tables are present in db 
     * and if the tables correspondents 
     * are in the Schema folder syncronized perfectly
     */
    public static function checkIfAllTablesExists() : array|false
    {
        $pdo = Connection::connect(new MySQL); 
        ENV::getContent();      

        $migrations = self::scandirCustom([]); 
 
        $migrations[] = 'migrations';

        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = self::queryTable(ENV::$config["DATABASE"],$placeHolders, true);

        $data = $pdo->prepare($sql); 
        $params = array_merge([ENV::$config["DATABASE"]], $migrations);
        $data->execute($params); 

        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN); 

        if (count($countTables) !== count($migrations)) return false;     

        return $countTables; 
    } 

    private static function scandirCustom(array $migrations)
    {
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
            if (!preg_match('/^20\d{2}_\d{2}_\d{2}_\d{6}_create_(.+)_table$/', $className, $matches)) exit("format name is invalid: `$migrate`" . PHP_EOL);
              
             $class = ucfirst($matches[1]); // es: Products 
             $fullClass = 'App\Core\Migration\Schema\\' . $class; // es: App\Core\Migration\Schema\Products

                $fullPath = $schemaDir . $migrate;

                if (!file_exists($fullPath)) exit("File `$fullPath` not found" . PHP_EOL);
                    
                    require_once $fullPath;

                    if (!class_exists($fullClass, false)) exit("Class `$class` not found in file `$migrate`" . PHP_EOL);
                      
                       $instance = new $fullClass;

                        if ($instance instanceof Migrations && method_exists($instance, 'up')) {
                            $migrations[] = strtolower($className);
                        }
            }
        }
         return $migrations;
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

    public static function removeOrphanTables() 
    {
        $pdo = Connection::connect(new MySQL); 
        ENV::getContent();      

        $migrations = self::scandirCustom([]);

        $migrations[] = 'migrations'; 
        $placeHolders = implode(",",array_fill(0, count($migrations), '?'));

        $sql = self::queryTable(ENV::$config["DATABASE"],'',false);

        $data = $pdo->prepare($sql); 
        $data->execute([ENV::$config["DATABASE"]]); 
        $countTables = $data->fetchAll(\PDO::FETCH_COLUMN);  

        if (count($countTables) !== count($migrations)) {
            
        $result=array_diff($countTables,$migrations);

            foreach ($result as $tableName) {
                $sql = "DROP TABLE IF EXISTS " . $tableName; 
                $data = $pdo->prepare($sql); 
                $data->execute(); 
            }
 
            return false; 
        } 

        return $countTables; 
    } 

    public static function insertInMigrationTable(string $table) : bool
    {  
        $pdo = Connection::connect(new MySQL); 

        $sql = "SELECT name,batch FROM migrations WHERE name = ?"; 
        $data = $pdo->prepare($sql);  
        $fetch = $data->execute([strtolower($table)]);    

        if (!$data->fetch()){
            $sql = "INSERT INTO migrations (name,batch) VALUES (?,?);"; 
            $data = $pdo->prepare($sql);  
            $fetch = $data->execute([strtolower($table),1]); 
            Logger::logMigration('migration.log','a+', $table, ' migrate with success');           
            return true;  
        }else { 
            $data = $pdo->prepare($sql);  
            $fetch = $data->execute([strtolower($table)]); 
            $fetch = $data->fetchAll();            
            $sql = "UPDATE migrations SET batch = ? WHERE name = ?"; 
            foreach ($fetch as $value) {
                $batch = $value['batch'] + 1;
                $data = $pdo->prepare($sql);  
                $fetch = $data->execute([$batch, strtolower($table)]); 
            }
            Logger::logMigration('migration.log','a+', $table, ' update with success', 'updated_at');
            return true;
        }

        return false; 
    } 

    public static function deleteInMigrationTable(string $table) 
    { 
        $pdo = Connection::connect(new MySQL); 

        $sql = "SELECT name FROM migrations WHERE name = ?;";

        $data = $pdo->prepare($sql);  
        $fetch = $data->execute([strtolower($table)]); 

        if ($data->fetch()){
         $sql = "DELETE FROM migrations WHERE name = ?"; 
 
         $data = $pdo->prepare($sql);  
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
               exit("syntax for this argument is not correct: \033[1m\033[3m".$string."\033[0m\n 
               it's correct : \033[1mcreate_\033[3mtablename\033[0m\033[1m_table\033[0m\n");
            }  

            if (!is_dir(__DIR__ . '/../Migration/Schema/')) {
                mkdir(__DIR__ . '/../Migration/Schema/', 0755, true);
            }

            $migrations = scandir(__DIR__ . '/../Migration/Schema/');  
           
            foreach ($migrations as $migration) {
              if($migration === '.' || $migration === '..') continue; 

              $migrationName = current(explode(".php",preg_replace('/^(20\d{2}_\d{2}_\d{2}_\d{6})_/', '', $migration))); 
      
             if ($string === $migrationName) exit('migration is already exists'.PHP_EOL) ;  
          
            } 

           $migrationName = preg_replace('/^(20\d{2}_\d{2}_\d{2}_\d{6})_/', '', $string);
         
            if (file_exists(str_replace("\\","/", "App\\Core\\Migration\\Schema\\". ucfirst($migrationName) . ".php"))) exit('File '.$migrationName.' already exists'); 
            
            $stringFile = date('Y_m_d_His_').$migrationName.'.php'; 

            if(empty(preg_match('/^(20)\d{2}_\d{2}_\d{2}_\d{6}_[a-zA-Z0-9_]+\.php$/', $stringFile, $matches))) exit('format is invalid' . PHP_EOL); 
           
            $nameClass = preg_replace(['/^create_/', '/_table$/'], '', $string);
            $nameClass = ucfirst($nameClass); 

            require_once __DIR__ . '/../Migration/Templates/migration_template.php';            
            
            $content = sprintf($template, $nameClass);
            file_put_contents(dirname(__DIR__) . '/Migration/Schema/'.$stringFile, $content);

        } catch (\Throwable $th) {
            exit('impossible to send a specific table ' . $th->getMessage());
        }
    } 

    public static function formatFileNameMigration(string $migration) : array 
   {
      if(file_exists($migration)) exit("The file `$migration` does not exist" . PHP_EOL);
       
      if (!str_contains($migration, '.php')) exit("this file migration `$migration` does not contains .php at the end".PHP_EOL); 
       
      $fileWithoutExtension = substr($migration, 0, strpos($migration, '.php'));
      
      if (!preg_match('/^20\d{2}_\d{2}_\d{2}_\d{6}_create_(.+)_table$/', $fileWithoutExtension, $matches)) exit("format name is invalid: `$migration`" . PHP_EOL); 

      return $matches; 
   }

} 

?>