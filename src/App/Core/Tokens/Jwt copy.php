<?php 
declare(strict_types=1); 

namespace App\Core\Tokens; 
use App\Interfaces\Token; 

class Jwt implements Token
{
   private string $secretKey; 

   public function __construct(string $key) 
   {
      $this->secretKey = $key; 
   }

   public function create(array $payload) 
   {
      $header = ["alg" => "RS256", "typ" => "JWT"]; 

      $base64UrlHeader = base64_encode(json_encode($header));  

      $base64UrlPayload = rtrim(base64_encode(json_encode($payload)), "="); 

      $dataToSign = $base64UrlHeader.".".$base64UrlPayload; 

      $this->secretKey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCsKHeCkhIWeKPp
sIT4Iae3GNJQZVebOiwnPZb4dt2/8fQ3U5mRwmrgHIierZTl6yI9IHhkjgQfqrrB
xMeTIx61bKzcJPVX97AfUf7D+0r9EFMhh9FjBYi9a3g1SGK54xC4Cb/AgFOpiRWb
XJz/TfZol8wBUNFS0PjlDKMk7kwhBu0Skqk6rC7fbQ9E1UG1PZZw6A7DWXYgHpGw
3gBhMR9FwKCvvrwoDJh4cxtu9vWmT+/PX/5txG7ZA70FCKY9Fh2K7OfdZ2F5zm+x
1oQ3vuYU1niO4sQalc0oG9apZQdSYiwvrOsU1JBWg3D1eDmPWkvytEk8O9SepmOW
FagHI7nnAgMBAAECggEAGPGqvJYc3XlQs/Jp7GiL8Wvnmn3PNLmNXjN+pV+A/70M
l9uau6jXbW4U9CYjFdvSKS3ZjnrulAaxFT7wH8c4ktbn248rUTnyddyeikI7JPGc
P0EmOy+5FqD0rjEKb56RHz6iXupOX5kc3VbiPzJKSptRPruOqMOIhz95Kp0FwMm6
kAk0O9kWij1EX57Vex7Cdm+BeMYGiGqgyYDauLmAS2l7q9N6XqABNUv40QOvi6dM
aKCJSnL+f+hOxvocSz7CwgRT4pv3RxzFsicuwcar6DrTKt4fki6HCFq/F1tFNrsM
n7/wOThT5EbU85D74xobSqWYvdbNgl9MHW7py3J6oQKBgQDq6ncR3QbwSb/ss871
r7sn2xpfOPY8uKYjJCssnW//e/Iyii1zcgHJe3i9a6FHnznl+XByvmd+CCjCNW1W
Qt+H6yJB19JeQS1neNjDrmRy/BtNKo6FKMQvquut10by/BSsNl+LjwxvtdKo24ng
wbjEMgj71gCqskgV5HlZVpWDsQKBgQC7nAyzx6a1MsjS9Sf8HUNyjU2TbFBs0oOR
1hVb1RxD6qY0RiPHDy3DAo3FSXF4FxhowPHSSvLWeplAZ3brJcvp+l1iNgFyiRov
XrzvO1al2KoKFkgxK0IaZXXo+j3gYHfqdgCjPbHYxdt/YV3uATR6EFHrqvRgUr1L
1PQ1LRV1FwKBgFIwQAJm8zOEifp1mlYI6pDyLlLcagIqlC29TMGqP3ICWmVmRKqc
W5WzdjXHf0DAq5ATak0q4qiMkD4KVTuV4AD8uhqROM+zPAB0nlowHILCQ4cG+aAK
+EC5KwXCSNdgbJcn2tvH65D160MatC2HeW5jFxOM9uTBxUiri+0+xsaxAoGBAJYe
y0c8Ndh+N5Yel400eVj7WpzhqgU5+g/DJ7og8AokhDQF//Dz42FM9NZt6z719BE2
ewoT8PbQiTqwz8ZfqyihrwG8RzI4JNzMyRABAleY5I9HvyKhA7cNgukW/FZDuxDA
tcfpwRq9T+NArSGakzwtPPAADqXY7yHz2CmI1senAoGBALUTKmkVOQUKdN8rLnik
DkP2MWkvssB3mxffZvUVsSMjq7y25W94CZvL73/WhzWthCsMQgr0DFC/wZXkeG3V
AVcRqvotHW0cUWNi9EZmdnWcxVOs2c3nhYi6sRTNLgAjm11DU7pR1iS9ru4BdVZA
tLQT8dJr6vBE0bjiRyGN+fD+
-----END PRIVATE KEY-----
EOD;

      $privateKey = openssl_pkey_get_private($this->secretKey);
      if (!$privateKey) {
    echo "Chiave non valida\n";
    echo openssl_error_string();
}
      

        $signature = '';
       $success = openssl_sign($dataToSign, $signature, $privateKey);
      if (!$success) {
         throw new Exception("Errore nella firma");
       }

       $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

      return $base64UrlHeader.".".$base64UrlPayload.".".$base64UrlSignature;
   } 

   private function validate(string $token) 
   {

   }

   private function decode(string $token) 
   {

   }

   private function expire() 
   {

   }

   public function generate() 
   {
    
   }
}




?>