<?php

namespace App\Utils;

class HttpHeaders
{
  public static function getAuthorization()
  {
    return getallheaders()['Authorization'] ?? '';
  }
}