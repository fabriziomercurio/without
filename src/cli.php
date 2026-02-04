<?php 

require_once "autoload.php"; 

$task = new \App\Core\Tasks\TaskMigrations; 
$seeder = new \App\Core\Tasks\TaskSeeders; 
 
$val = $argv[1] ?? NULL; 

if (!$val) {
    echo "add an argument, like 'migrate:all'" . PHP_EOL; 
    exit;
}

$commands = [
'migrate:all' => 'run all migrations',
'migrate:clean' => 'delete migrations in table if they are not sync in folder',
'migrate:down' => 'delete all migrations',
'migrate:up' => 'up a single migration with name passed like argument',
'migrate:delete' => 'delete a single migration with name passed like argument',
'create:migration' => 'create an automatic file migration',
'create:seeder' => 'create an automatic seeder file',
'run:seeder' => 'runs a seeder file']; 

if (!array_key_exists($val,$commands)) {
    if ($val !== 'commands:all') echo ' il comando non esiste ' . PHP_EOL;   
       
    echo '- comandi disponibili: ' . PHP_EOL; 

    foreach ($commands as $key => $value) echo $key .' => '. $value . PHP_EOL; exit();
}

try {
    $result = match ($val) {
    $keys = array_keys($commands), 
    $keys[0] => $task->upAllMigrations(), // migrate:all
    $keys[1] => $task->cleanMigrations(), // migrate:clean
    $keys[2] => $task->downMigrations(), // migrate:down
    $keys[3] => isset($argv[2]) ? $task->upSingleMigration($argv[2]) : exit("missing argument" . PHP_EOL), // migrate:up
    $keys[4] => isset($argv[2]) ? $task->deleteSingleMigration($argv[2]) : exit("missing argument" . PHP_EOL), // migrate:delete
    $keys[5] => isset($argv[2]) ? $task->createMigration($argv[2]) : exit("missing argument" . PHP_EOL), // create:migration 
    $keys[6] => isset($argv[2]) ? $seeder->createSeeder($argv[2]) : throw new \Exception("missing argument" . PHP_EOL), // create:seeder
    $keys[7] => isset($argv[2]) ? $seeder->runSeeder($argv[2]) : exit("missing argument" . PHP_EOL), // run:seeder

 };
} catch (\Exception $th) { 
    exit($th->getMessage() . PHP_EOL); 
}
 
 

?>