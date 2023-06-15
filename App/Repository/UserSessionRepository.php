<?php

namespace App\Repository;

use App\Repository\ServerErrorRepository;
use App\Utils\GenerateAleatoryString;
use Exception;

/**
 * Manipula as sessões de usuário no banco de dados.
 */
class UserSessionRepository extends Database
{

  /**
   * Gera um token não existente no banco para a API.
   * 
   * @return string|null
   */
  private static function generateSessionToken(): string|null
  {
    try {
      $loop = true;
      while ($loop) {
        $sessionToken = GenerateAleatoryString::generateAlphaNumeric(64);

        $script = 'SELECT session_token FROM ' . self::USER_SESSIONS_TABLE .
          ' WHERE session_token = :session_token;';

        $query = self::$connection->prepare($script);
        $query->bindValue(':session_token', $sessionToken);
        $query->execute();

        if ($query->rowCount() > 0) {
        } {
          $loop = false;
        }
      }

      return $sessionToken;
    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);

      return null;
    }
  }

  /**
   * Cria uma nova sessão para um usuário.
   * @param string $email
   * @return array
   */
  public static function createSession(string $email): array
  {
    try {
      $sessionToken = self::generateSessionToken();

      $script = '
        INSERT INTO ' . self::USER_SESSIONS_TABLE . ' 
          (session_token, user_id) 
        VALUES (
          :session_token, 
          (SELECT id FROM ' . self::USER_TABLE . '
            WHERE email = :email)
        );
        ';

      $query = self::$connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->bindValue(':email', $email);
      $query->execute();

      return [
        'status' => 'OK',
        'session_token' => $sessionToken
      ];
    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);
      http_response_code(500);

      return [
        'status' => 'SERVER ERROR'
      ];
    }
  }

  /**
   * Verifica a validade de uma sessão de usuário.
   * @param string $sessionToken
   * @return array
   */
  public static function checkSessionValidate(string $sessionToken): array
  {
    try {
      $script = 'SELECT session_token FROM ' .
        self::USER_SESSIONS_TABLE .
        ' WHERE session_token = :session_token;';

      $query = self::$connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->execute();

      if ($query->rowCount() > 0) {

        return [
          'status' => 'OK',
          'valid' => true
        ];
      } {

        return [
          'status' => 'OK',
          'valid' => false,
          'reason' => 'SESSION DOES NOT EXISTS'
        ];
      }
    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);
      http_response_code(500);

      return [
        'status' => 'SERVER ERROR'
      ];
    }
  }

  /**
   * Exclui uma sessão de usuário.
   * 
   * @param string $jsonData
   * @return array
   */
  public static function closeSession(string $sessionToken): array
  {
    try {
      $sessionValid = self::checkSessionValidate($sessionToken);
      if (!$sessionValid['valid']) {

        return [
          'status' => 'OK',
          'closed' => false,
          'reason' => 'SESSION DOES NOT EXISTS'
        ];
      }

      $script = 'DELETE FROM ' . self::USER_SESSIONS_TABLE . ' WHERE session_token = :session_token;';
      $query = self::$connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->execute();

      return [
        'status' => 'OK',
        'closed' => true
      ];
    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);
      http_response_code(500);

      return [
        'status' => 'SERVER ERROR'
      ];
    }
  }

}