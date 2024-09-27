<?php

require_once(__DIR__ . "/Router.php");
require_once(__DIR__ . "/../utils/Variable.php");

class ToolRouter
{

  public function action(string $method, string $controller, ?string $idParam = null)
  {
    try
    {      
      $method = strpos($method, "?") ? substr($method, 0, strpos($method, "?")) : $method;

      require_once(__DIR__ . "/../controllers/" . $controller . "Controller.php");
      $controller = new ToolController();
      return $response = OKResponse::response_ok((new ToolController())->$method());
    }
    catch (Exception $e)
    {        
      header("HTTP/1.1 500 Internal Server Error");
      throw $e;
    }

  }
}

//$this->requestBody = json_decode(file_get_contents('php://input'), true);
//$this->queryParams = $_GET;