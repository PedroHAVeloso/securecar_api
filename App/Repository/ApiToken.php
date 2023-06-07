<?php

namespace App\Repository;

use Exception;

class ApiToken extends Database
{
  public static function checkApiTokenValidity(string $api_token)
  {
    try {
      $connection = self::connect();

      $script =
        'SELECT api_token FROM ' . self::API_TOKEN_TABLE .
        ' WHERE api_token = :api_token;';

      $query = $connection->prepare($script);

      $query->bindValue(':api_token', $api_token);

      $query->execute();

      if ($query->rowCount() > 0) {
        return 200;
      } else {
        return 400;
      }
    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      return 500;
    } finally {
      self::close($connection);
    }
  }
}