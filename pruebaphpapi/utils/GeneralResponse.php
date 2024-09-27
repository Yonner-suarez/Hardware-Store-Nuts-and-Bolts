<?php

class GeneralResponse implements JsonSerializable 
{
  private $message;
  private $status;
  private $data;

  public function __construct(string $message, string $status, $data)
  {
    $this->message = $message;
    $this->status = $status;
    $this->data = $data;
  }

   public function jsonSerialize() {
        return [
            'message' => $this->message,
            'status' => $this->status,
            'data' => $this->data,
        ];
    }
}
