<?php

namespace App\Repository;

use Exception;

/**
 * Realiza interações com os Tokens de uso da API.
 */
class ApiTokenRepository extends Database
{

  /**
   * Checa a validade de um Token de uso da API.
   * 
   * Deve receber uma [string] com o Token.
   * 
   * Retorna um valor [bool] com o resultado da verificação.
   * 
   * Caso erro, retorna [null].
   * 
   * @param string $apiToken
   * @return bool|null
   */
  public static function checkApiTokenValidity(string $apiToken): bool|null
  {
    try {
      $script =
        'SELECT api_token FROM ' . self::API_TOKEN_TABLE .
        ' WHERE api_token = :api_token;';

      $query = self::$connection->prepare($script);

      $query->bindValue(':api_token', $apiToken);

      $query->execute();

      if ($query->rowCount() > 0) {

        return true;
      } else {

        return false;
      }
    } catch (Exception $exc) {
      ServerErrorRepository::addError($exc);
      http_response_code(500);

      return null;
    }
  }
}