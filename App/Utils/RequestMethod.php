<?php

namespace App\Utils;

class RequestMethod
{
  public const METHOD_GET = 'GET';
  public const METHOD_POST = 'POST';
  public const METHOD_PUT = 'PUT';
  public const METHOD_DELETE = 'DELETE';

  static public function getRequestMethod()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    return $method;
  }
}