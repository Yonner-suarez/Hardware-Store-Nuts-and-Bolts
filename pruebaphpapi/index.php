<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once(__DIR__ . "/Routes/Router.php");
require_once(__DIR__ . "/config.php");

// Middleware para devolver JSON
function jsonMiddleware($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$router = new Router();
$response = $router->matchRoute($uri);
jsonMiddleware($response);




//LoadModule rewrite_module modules/mod_rewrite.so
//Esto permite que el .htaccess funcione


//<Directory /ruta/a/tu/sitio/web>
//    AllowOverride All
//</Directory>







