<?php

namespace App\Repository;

use App\Repository\ServerErrorRepository;
use App\Utils\GenerateAleatoryString;
use Exception;

class UserSessionRepository extends Database
{
  private static function generateSessionToken(): string
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

      return '';
    }
  }

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

  public static function checkSessionValidate($sessionToken)
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

  public static function closeSession(object $jsonData)
  {
    try {
      $sessionToken = $jsonData->session_token;

      $connection = self::connect();

      $script = 'SELECT session_token FROM ' . self::USER_SESSIONS_TABLE . ' WHERE session_token = :session_token;';
      $query = $connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->execute();

      if ($query->rowCount() > 0) {
        $script = 'DELETE FROM ' . self::USER_SESSIONS_TABLE . ' WHERE session_token = :session_token;';
        $query = $connection->prepare($script);
        $query->bindValue(':session_token', $sessionToken);
        $query->execute();

        return [
          'status' => 200,
          'closed' => 'YES'
        ];
      } {
        return [
          'status' => 200,
          'closed' => 'NO',
          'reason' => 'SESSION DOES NOT EXISTS'
        ];
      }

    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    } finally {
      self::close($connection);
    }
  }

}