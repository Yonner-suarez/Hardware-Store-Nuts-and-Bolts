<?php
require_once(__DIR__ . "/../utils/Variable.php");
class DefaultRouter{

  public function action(string $method, string $controller, ?string $idParam = null)
  {
    try
    {      
      $method = strpos($method, "?") ? substr($method, 0, strpos($method, "?")) : $method;
      require_once(__DIR__ . "/../controllers/" . $controller . "Controller.php");
      $controller = new DefaultController();
      $response = $controller->$method();
      return $response = OKResponse::response_ok((new DefaultController())->$method());
    }
    catch (Exception $e)
    {        
      header("HTTP/1.1 500 Internal Server Error");
      throw $e;
    }

  }
  
  
}