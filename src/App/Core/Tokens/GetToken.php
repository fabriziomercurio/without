<?php 
declare(strict_types=1); 

namespace App\Core\Tokens; 
use App\Interfaces\Token; 

class GetToken 
{
   private Token $token; 

   public function __construct(Token $t) 
   {
      $this->token = $t;
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