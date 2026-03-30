<?php

namespace App\Middlewares;

use App\Core\Tokens\Jwt;

class JWTvalidate
{
    private static ?Jwt $jwt = null;

    public static function validate(): bool
    {
        if (self::$jwt === null) {
            self::$jwt = new Jwt('private.key');
        }

        $auth = getallheaders()['Authorization'] ?? null;

        if (!$auth || $auth === "Bearer null") {
            http_response_code(401);
            echo json_encode(["error" => "Token mancante"]);
            exit;
        }

        $token = str_replace('Bearer ', '', $auth);
        $token = trim($token, "\"");

        try {
            return self::$jwt->validate($token, 'public.key');
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => $e->getMessage()]);
            exit;
        }
    }
}
