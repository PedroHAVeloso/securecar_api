<?php

namespace App\Routes;

use App\Utils\RequestMethod;
use App\Utils\RequestUri;

class Route
{
  public static function createRoute(string $method, string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == $method
    ) {
      $function();
    }
  }
}