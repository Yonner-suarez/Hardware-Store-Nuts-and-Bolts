<?php
require_once(__DIR__ . "/../utils/Variable.php");
require_once(__DIR__ . "/../infraestructure/middleware.php");
require_once(__DIR__ . "/../utils/Services/Token.php");

class UserRouter
{
  private $method;

  public function action(string $method, string $controller, ?string $idParam = null)
  {
    try
    { 
      $token = Token::getToken();
      Middleware::validateToken($token);

      $this->method = strpos($method, "?") ? substr($method, 0, strpos($method, "?")) : $method;
      require_once(__DIR__ . "/../controllers/" . $controller . "Controller.php");
      return OKResponse::response_ok((new UserController())->$method());
    }
    catch (InternalServerErrorResponse $e)
    {        
      throw $e;
    }

  }
}
