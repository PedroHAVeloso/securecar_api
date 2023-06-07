<?php

namespace App\Repository;

use Exception;

class ServerError extends Database
{
  private const ERROR_TABLE = 'tb_server_error_log';

  public static function addError(string $error)
  {
    try {
      $connection = self::connect();

      $script =
        'INSERT INTO ' . self::ERROR_TABLE .
        ' (error) VALUES (:error);';

      $query = $connection->prepare($script);
      $query->bindValue(':error', $error);

      $query->execute();

      return true;
    } catch (Exception $exc) {
      return false;
    } finally {
      self::close($connection);
    }
  }
}