<?php
declare(strict_types=1);

namespace App\Core\Tokens;

use App\Interfaces\Token;

class Jwt implements Token
{
    public function __construct(private string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function create(array $payload): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        $base64UrlHeader = rtrim(strtr(base64_encode(json_encode($header, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');

        $dataToSign = $base64UrlHeader . '.' . $base64UrlPayload;

        $path = realpath(__DIR__ . '/../../../' . $this->secretKey);
        if (!$path || !file_exists($path)) {
            throw new \Exception("Chiave privata non trovata: $path");
        }

        $keyContent = file_get_contents($path);
        $privateKey = openssl_pkey_get_private($keyContent);
        if (!$privateKey) {
            throw new \Exception("Chiave privata non valida: " . openssl_error_string());
        }

        $success = openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);       

        if (!$success) {
            throw new \Exception("Errore nella firma");
        }

        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    public function validate(string $token, string $publicKeyPath): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Exception("Token JWT non valido");
        }

        [$header, $payload, $signature] = $parts; 

        $dataToVerify = $header . '.' . $payload;
        $signatureDecode = base64_decode(strtr($signature, '-_', '+/'));

        $path = realpath(__DIR__ . '/../../../' . $publicKeyPath);
        if (!$path || !file_exists($path)) {
            throw new \Exception("Chiave pubblica non trovata: $path");
        }

        $keyContent = file_get_contents($path);
        $publicKey = openssl_pkey_get_public($keyContent);
        if (!$publicKey) {
            throw new \Exception("Chiave pubblica non valida: " . openssl_error_string());
        }

        $isValid = openssl_verify($dataToVerify, $signatureDecode, $publicKey, OPENSSL_ALGO_SHA256);
        
        if ($isValid === 1) {
            return true;
        } elseif ($isValid === 0) {
            throw new \Exception("Firma non valida");
        } else {
            throw new \Exception("Errore nella verifica: " . openssl_error_string());
        }
    }

    public function isExpired(array $payload) : bool
    {
       if (!isset($payload['exp']) || !is_numeric($payload['exp'])) {
         return true;
       }
       return time() > $payload['exp'];
    }
}
