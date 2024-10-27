<?php

require_once(__DIR__ . "/Router.php");
require_once(__DIR__ . "/../utils/Variable.php");
require_once(__DIR__ . "/../infraestructure/middleware.php");
require_once(__DIR__ . "/../utils/Services/Token.php");

class QuotesRouter
{

  public function action(string $method, string $controller, ?string $idParam = null)
  {
    try
    {  
       
      $method = strpos($method, "?") ? substr($method, 0, strpos($method, "?")) : $method;

      require_once(__DIR__ . "/../controllers/" . $controller . "Controller.php");
      $controller = new QuotesController();
      return OKResponse::response_ok((new QuotesController())->$method());
    }
    catch (Exception $e)
    {        
      throw $e;
    }

  }
}