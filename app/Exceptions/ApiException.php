<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
  public $status = 400;

  public function __construct($message, $status)
  {
    parent::__construct($message);
    $this->status = $status;
  }

  public function render($request)
  {
    return response()->json([
      'message' => $this->getMessage()
    ], $this->status);
  }
}
