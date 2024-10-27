<?php

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . "/../utils/Variable.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Middleware {

  // Middleware para devolver JSON
  public static function jsonMiddleware($response, $status = 200) {
      http_response_code($status); 
      header('Content-Type: application/json');
      echo json_encode($response);
      exit();
  }

  public static function authMiddleware($data) {
    return (new GeneralResponse("Success", 200, $data))->jsonSerialize();
  }

  public static function getToken(int $tipoPersona, string $correo, string $username) {
    global $jwtKey;
    global $jwtExp;

    
    $role = 'User'; 
    switch ($tipoPersona) {
        case 1: 
            $role = 'Admin';
            break;
        case 2: 
            $role = 'Permanente';
            break;
        case 3: 
            $role = 'Periódico';
            break;
        case 4: 
            $role = 'Casual';
            break;
        case 5: 
            $role = 'Nuevo';
            break;
    }

    $payload = [
        'iss' => $correo,
        'aud' => $username,
        'iat' => time(), 
        'nbf' => time(), 
        'exp' => time() + $jwtExp,
        'role' => $role 
    ];
    $token = JWT::encode($payload, $jwtKey, 'HS256');
    return $token;
  }

  public static function validateToken($token) {
    global $jwtKey;
    try {
        $decoded = JWT::decode($token, new Key($jwtKey, 'HS256'));
        return $decoded;
    } catch (ExpiredException $e) {
        throw new UnauthorizedResponse("Token expired");
    } catch (\Exception $e) {
        throw new UnauthorizedResponse("Invalid token");
    }
  }
}


?>
