<?php
require_once 'config.php'; 
require_once 'Middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email']; 

    
    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

   
    if ($conn->connect_error) {
        Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos'], 500);
    }

   
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        Middleware::jsonMiddleware(['error' => 'El nombre de usuario o el correo ya están en uso'], 400);
    } else {
       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        
        $tipoPersona = 5; 
        $stmt = $conn->prepare("INSERT INTO users (username, password, tipo_persona, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $username, $hashedPassword, $tipoPersona, $email);

        if ($stmt->execute()) {
            Middleware::jsonMiddleware(['success' => 'Usuario registrado exitosamente']);
        } else {
            Middleware::jsonMiddleware(['error' => 'Error al registrar el usuario'], 500);
        }
    }
}
?>
