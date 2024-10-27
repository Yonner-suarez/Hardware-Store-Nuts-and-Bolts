<?php

require_once(__DIR__ . "/../models/Tools.php");
require_once (__DIR__ . "/../config.php");


class ToolController
{
    public function Tools()
    {
      try {
        $method_request = $_SERVER['REQUEST_METHOD'];
        if($method_request == "GET")
        {
          global $host, $dbUser, $dbPassword, $dbName, $dbPort;
          $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

          if ($conn->connect_error) {
            Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
        }

          // Consulta para obtener los productos
          $stmt = $conn->prepare("SELECT id, name, price, marca, puntuacion, code FROM products");
          $stmt->execute();
          $stmt->bind_result($id, $name, $price, $marca, $puntuacion, $code);

          $ToolsResponse = [];

          while ($stmt->fetch()) {
    // Agregar cada producto al array
    $ToolsResponse[] = [
        "id" => $id,
        "name" => $name,
        "price" => $price,
        "marca" => $marca,
        "puntuacion" => $puntuacion,
        "code" => $code,
    ];
}

          // Cerrar el statement
          $stmt->close();

          // Cerrar la conexión
          $conn->close();

          // Devolver el arreglo con los datos
          return $ToolsResponse;
        }
        throw new BadRequestResponse("Metodo no permitido");
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function Tool()
    {
      try {
        $tools = [
         new Tools("Tool 1", "Description 1", 100, 10, "image1.jpg"),
         new Tools("Tool 2", "Description 2", 200, 20, "image2.jpg"),
         new Tools("Tool 3", "Description 3", 300, 30, "image3.jpg"),
         new Tools("Tool 4", "Description 4", 400, 40, "image4.jpg"),
         new Tools("Tool 5", "Description 5", 500, 50, "image5.jpg"),
         ];

         return $tools;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

     public function ToolsOptions()
    {
      try {
        $method_request = $_SERVER['REQUEST_METHOD'];
        if($method_request == "GET")
        {
          global $host, $dbUser, $dbPassword, $dbName, $dbPort;
          $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

          if ($conn->connect_error) {
            Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
        }

          // Consulta para obtener los productos
          $stmt = $conn->prepare("SELECT id, name FROM products");
          $stmt->execute();
          $stmt->bind_result($id, $name);

          $ToolsResponse = [];

          while ($stmt->fetch()) {
              // Agregar cada producto al array
              $ToolsResponse[] = [
                  "value" => $id,
                  "label" => $name
              ];
          }

          // Cerrar el statement
          $stmt->close();

          // Cerrar la conexión
          $conn->close();

          // Devolver el arreglo con los datos
          return $ToolsResponse;
        }
        throw new BadRequestResponse("Metodo no permitido");
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    
}