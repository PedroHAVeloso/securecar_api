<?php

namespace App\Utils;

class RequestUri
{
  public static function getRequestUri()
  {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestUri = explode('?', $requestUri);
    return $requestUri[0];
  }
}