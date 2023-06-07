<?php

namespace App\Repository;

use App\Utils\ErrorReport;
use App\Utils\GenerateAleatoryString;
use Exception;

class UserSession extends Database
{
  private static function generateSessionToken()
  {
    try {
      $connection = self::connect();

      $loop = true;
      while ($loop) {
        $sessionToken = GenerateAleatoryString::generateAlphaNumeric(64);

        $script = 'SELECT session_token FROM ' . self::USER_SESSIONS_TABLE . ' WHERE session_token = :session_token;';
        $query = $connection->prepare($script);
        $query->bindValue(':session_token', $sessionToken);
        $query->execute();

        if ($query->rowCount() > 0) {
        } {
          $loop = false;
        }
      }

      return $sessionToken;
    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    } finally {
      self::close($connection);
    }
  }

  public static function createSession(?object $jsonData)
  {
    try {
      $connection = self::connect();

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

      $query = $connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->bindValue(':email', $jsonData->email);
      $query->execute();

      return $sessionToken;
    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    } finally {
      self::close($connection);
    }
  }

  public static function checkSessionValidate(object $jsonData)
  {
    try {
      $sessionToken = $jsonData->session_token;

      $connection = self::connect();

      $script = 'SELECT session_token FROM ' . self::USER_SESSIONS_TABLE . ' WHERE session_token = :session_token;';
      $query = $connection->prepare($script);
      $query->bindValue(':session_token', $sessionToken);
      $query->execute();

      if ($query->rowCount() > 0) {
        return [
          'status' => 200,
          'valid' => true
        ];
      } {
        return [
          'status' => 200,
          'valid' => false,
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