<?php

namespace App\Utils;

class Env
{
  public static function getEnv()
  {
    $env = parse_ini_file(".env");
    return $env;
  }
}