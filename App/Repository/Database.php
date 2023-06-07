<?php

namespace App\Repository;

use App\Utils\Env;
use Exception;
use PDO;

class Database
{
  public const USER_TABLE = 'tb_user';
  public const USER_SESSIONS_TABLE = 'tb_user_sessions';
  public const API_TOKEN_TABLE = 'tb_api_token';
  public const USER_VALIDATION_TABLE = 'tb_user_validation';

  public static function connect()
  {
    try {
      $env = Env::getEnv();

      $connection = new PDO(
        $env['DB_DRIVE']
        . ':dbname=' . $env['DB_NAME']
        . ';host=' . $env['DB_HOST'],
        $env['DB_USER'],
        $env['DB_PASS']
      );

      return $connection;
    } catch (Exception $exc) {
      return false;
    }
  }

  public static function close(&$connection)
  {
    $connection = null;
    return $connection;
  }
}