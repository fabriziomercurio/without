<?php

namespace App\Middlewares;
use App\Core\Tokens\BlackList\BlackListTokens; 

use App\Core\Tokens\Jwt;

class JwtValidate
{
    private static ?Jwt $jwt = null;

    public static function validate(): bool
    {
        $jwt = new Jwt('private.key');
        

        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null; 

        if (!$auth || $auth === "Bearer null") {
            http_response_code(401);
            echo json_encode(["error" => "Token mancante"]);
            exit;
        }

        $token = str_replace('Bearer ', '', $auth);
        $token = trim($token, "\"");

        try {  
            $payload = $jwt->decodePayload($token);  
            
            $blacklist = new BlackListTokens;  

            if ($blacklist->contains($payload['jti'])) {
            http_response_code(401);
            echo json_encode(["error" => "Token revocato"]);
            exit;
            }

            return $jwt->validate($token, 'public.key');
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => $e->getMessage()]);
            exit;
        }
    } 
}
