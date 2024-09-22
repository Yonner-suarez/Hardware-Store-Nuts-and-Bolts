<?php
// Configurar encabezados para JSON
header("Content-Type: application/json");

// Manejar diferentes rutas
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($request_uri) {
    case '/':
        if ($method === 'GET') {
            echo json_encode(["mensaje" => "Bienvenido a la API"]);
        }
        break;
    
    case '/usuarios':
        if ($method === 'GET') {
            echo json_encode(["usuarios" => ["Juan", "María", "Carlos"]]);
        }
        break;
    
    case '/docs':
        // Servir la documentación de Swagger UI
        header('Content-Type: text/html');
        readfile('swagger-ui/index.html');
        exit;
        
    case '/openapi.json':
        // Servir el archivo de especificación OpenAPI
        header('Content-Type: application/json');
        readfile('openapi.json');
        exit;
    
    default:
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
        break;
}