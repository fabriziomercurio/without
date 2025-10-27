<?php
declare(strict_types=1);

namespace App\Core\Logging;

class Logger
{
    private static function createFolder() : string 
    {
        $dir = dirname(__DIR__) . '/Logs/';

        if (!is_dir($dir)) { 
            mkdir($dir, 0755, true); 
            if (!is_writable($dir)) {
                error_log("this folder $dir is not writable");
            }
        }

        return $dir;
    }

    public static function logMigration(string $filename, string $mode, ?string $tableName = null, ?string $print = null, string $type = 'created_at'): void
    {
        $dir = self::createFolder();
        $filePath = $dir . $filename;
           
        $batch = 'batch(1)'; 
        $fp = fopen($filePath, $mode);
        $toPrint = $type . '[' . date("Y-m-d H:i:s") . '] ✅ ' . ($tableName ? "Table `" . strtolower($tableName) . "` " : '') . $print. ' ' .$batch . PHP_EOL;
        fwrite($fp, $toPrint);
        fclose($fp);
    }    
}