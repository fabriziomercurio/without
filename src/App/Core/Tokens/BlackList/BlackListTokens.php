<?php

namespace App\Core\Tokens\BlackList; 
use App\Core\Connections\Connection; 
use App\Core\Connections\Redis; 
use Predis\Client;

class BlackListTokens 
{
    private \Predis\Client $redis; 

    public function __construct() 
    {
       $this->redis = Connection::connect(new Redis); 
    }

    public function add(string $jti, int $exp) 
    {
      
       $ttl = $exp - time(); 
       if ($ttl > 0) {  
          $this->redis->set("blacklist:$jti", "revoked", "EX", $ttl);
        } 
    } 

    //this method return number 1 value if key has been revoked
    public function contains(string $jti): bool
    {
       return $this->redis->exists("blacklist:$jti") === 1;
    }

} 


?>