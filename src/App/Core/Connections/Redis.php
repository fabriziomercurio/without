<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database;
use Predis\Client;


class Redis implements Database 
{
    private static $redis; 

    public static function connect() : \Predis\Client
    {
        return new Client([
            'scheme' => 'tcp',
            'host'   => 'redis', // docker-compose name service 
            'port'   => 6379,
        ]);
    }  
}

?> 
