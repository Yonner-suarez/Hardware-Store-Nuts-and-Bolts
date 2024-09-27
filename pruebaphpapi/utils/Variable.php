<?php

require_once(__DIR__ . "/GeneralResponse.php");
class OKResponse
{
   public static function response_ok($data)
    {
       return (new GeneralResponse("Success", 200, $data))->jsonSerialize();
    }
}
class BadRequestResponse
{
    public  function __construct(object $data)
    {
      return (new GeneralResponse("Success", 400, $data))->jsonSerialize();
    }
}
class InternalServerErrorResponse
{
    public  function __construct(object $data)
    {
      return (new GeneralResponse("Success", 500, $data))->jsonSerialize();
    }
}
class UnauthorizedResponse
{
    public  function __construct(object $data)
    {
      return (new GeneralResponse("Success", 401, $data))->jsonSerialize();
    }
}