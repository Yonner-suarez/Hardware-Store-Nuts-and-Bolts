<?php
require_once 'config.php'; 
require_once 'Middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

    
    if ($conn->connect_error) {
        Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
    }

    
    $stmt = $conn->prepare("SELECT id, password, tipo_persona FROM users WHERE username = ?");
    if (!$stmt) {
        Middleware::jsonMiddleware(['error' => 'Error en la consulta: ' . $conn->error], 500);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $tipoPersona);
    $stmt->fetch();

    
    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        
        $token = Middleware::getToken($tipoPersona, $username, $userId);
        Middleware::jsonMiddleware(['token' => $token]);
    } else {
        Middleware::jsonMiddleware(['error' => 'Credenciales inválidas'], 401);
    }

    
    $stmt->close();
    $conn->close();
}
?>
