<?php

namespace App\Routes;

use App\Utils\RequestMethod;
use App\Utils\RequestUri;

class Route
{
  public static function get(string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == RequestMethod::METHOD_GET
    ) {
      $function();
    }
  }

  public static function post(string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == RequestMethod::METHOD_POST
    ) {
      $function();
    }
  }

  public static function delete(string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == RequestMethod::METHOD_DELETE
    ) {
      $function();
    }
  }

  public static function put(string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == RequestMethod::METHOD_PUT
    ) {
      $function();
    }
  }
}