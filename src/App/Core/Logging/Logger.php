<?php
declare(strict_types=1);

namespace App\Core\Logging;
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

class Logger
{
    private static function createFolder() : string 
    {
      $dir = dirname(__DIR__) . '/../Logs/';

        if (!is_dir($dir)) { 
            mkdir($dir, 0755, true); 
            if (!is_writable($dir)) {
                error_log("this folder $dir is not writable");
            }
        }

        return $dir;
    }

    public static function logMigration(string $filename, string $mode, ?string $tableName = null, ?string $print = null, string $type = 'created_at', bool $code = true)
    {
        $dir = self::createFolder();
        $filePath = $dir . $filename;

        if($code === false){
           return self::logError($filePath,$mode,$print);
        } 
           
        if ($tableName) { 
           $batch = ($tableName) ? self::fetchBatch($tableName) : null;
        }
        
        $fp = fopen($filePath, $mode);
        $toPrint = $type . '[' . date("Y-m-d H:i:s") . '] ✅ ' 
        . ($tableName ? "Table `" . strtolower($tableName) . "`" : '') . $print. ' ' . ($tableName ? $batch : '' ) . PHP_EOL;
        fwrite($fp, $toPrint);
        fclose($fp);
    }  
    
    public static function logError(string $filePath, string $mode, string $print) 
    {
        $fp = fopen($filePath, $mode);
        $toPrint = 'error at hours ' . '[' . date("Y-m-d H:i:s") . '] ❌ Reason : ' . $print  . PHP_EOL;
        fwrite($fp, $toPrint);
        fclose($fp);
    }

    public static function fetchBatch(string $tableName) : string|bool
    {
       $pdo = Connection::connect(new MySQL); 
       $stmt = $pdo->prepare("SELECT batch FROM migrations WHERE name=:name"); 
       $stmt->execute(['name' => $tableName]);
       $result = $stmt->fetch(); 
       if ($result !== false) {   
        return 'batch('.$result['batch'].')';
       }      
       return false; 
    }
}