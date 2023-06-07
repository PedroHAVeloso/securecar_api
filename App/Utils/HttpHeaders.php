<?php

namespace App\Utils;

class HttpHeaders
{
  public static function getAllHeaders()
  {
    return getallheaders();
  }

  public static function getAuthorization()
  {
    return getallheaders()['Authorization'] ?? '';
  }
}