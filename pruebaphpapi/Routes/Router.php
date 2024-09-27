<?php

class Router{

  private $router;
  private $method;
  private $idParam;

  public function matchRoute($uri)
  {
    try{
    if($uri == "/"){
      $this->router = "default";
      $this->method = "login";
    }else{
      $routeParts = explode("/", $uri);
      $this->router = $routeParts[1];
      $this->method = $routeParts[2];
      $this->idParam = $routeParts[3] !== "" ? $routeParts[3] : null;
    }

    return $this->redirectRouter();
    }
    catch (Exception $e)
    {        
      header("HTTP/1.1 500 Internal Server Error");
      return new InternalServerErrorResponse(['error' => $e->getMessage()]);
    }
  }

   public function redirectRouter() 
   {
    try{
      require_once(__DIR__ . "/". ucfirst($this->router) . "Router.php");
      $routerClass = ucfirst($this->router) . "Router";
      $router = new $routerClass();

      return $response = $router->action($this->method, ucfirst($this->router), $this->idParam);
    }
    catch (Exception $e)
    {        
      header("HTTP/1.1 500 Internal Server Error");
      throw $e;
    }
   }


}
