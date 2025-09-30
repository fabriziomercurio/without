<?php 
declare(strict_types=1); 

namespace App\Core\Tokens; 
use App\Interfaces\Token; 

class GetToken 
{  
   public function __construct(private Token $token) 
   {
      $this->token = $token;
   } 

   public function create(array $payload) 
   {
     return $this->token->create($payload); 
   }

   public function validate(string $token, string $publicKey) 
   {
     return $this->token->validate($token,$publicKey); 
   } 

   public function isExpired(array $payload) 
   {
     return $this->token->isExpired($payload);
   } 
}

?>