<?php
require_once(__DIR__ . "/../utils/Variable.php");

class UserRouter
{
  private $method;

  public function action(string $method, string $controller, ?string $idParam = null)
  {
    try
    { 
      $this->method = strpos($method, "?") ? substr($method, 0, strpos($method, "?")) : $method;
      require_once(__DIR__ . "/../controllers/" . $controller . "Controller.php");
      return $response = OKResponse::response_ok((new UserController())->$method());
    }
    catch (Exception $e)
    {        
      header("HTTP/1.1 500 Internal Server Error");
      return new InternalServerErrorResponse(['error' => $e->getMessage()]);
    }

  }
}
