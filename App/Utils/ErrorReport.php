<?php

namespace App\Utils;

class ErrorReport
{
  public static function displayErrorToUser(int $errorCode, string $errorMessage)
  {
    http_response_code($errorCode);
    $response = array('status' => $errorCode, 'error' => $errorMessage);
    echo json_encode($response);
  }
}