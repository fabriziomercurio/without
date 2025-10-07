<?php 

require_once "autoload.php"; 

$task = new \App\Core\TaskManager; 

$val = $argv[1] ?? NULL; 

if (!$val) {
    echo "add an argument, like 'migrate:all'" . PHP_EOL; 
    exit;
}

$result = match ($val) {
    'migrate:all' => $task->upAllMigrations(), 
}; 
// var_dump($result); 
 

?>