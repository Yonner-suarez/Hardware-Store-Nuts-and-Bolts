<?php

$filepath = dirname($_SERVER['SCRIPT_NAME']);
$path = $_SERVER['REQUEST_URI'];
$uri = substr($path, strlen($filepath));


$envFile = __DIR__ . '/.env';
$envVariables = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $envVariables[trim($key)] = trim($value);
        }
    }
}

$host = $envVariables['HOST'] ?? 'localhost'; 
$dbName = $envVariables['DB_NAME'] ?? 'hardware_nuts_db';
$dbUser = $envVariables['DB_USER'] ?? 'root'; 
$dbPassword = $envVariables['DB_PASSWORD'] ?? 'JudaSaki2916*'; 
$dbPort = $envVariables['DB_PORT'] ?? '3306'; 
$jwtKey = $envVariables['JWT_KEY'] ?? 'your_jwt_secret';
$jwtExp = $envVariables['JWT_EXP'] ?? 3600;
