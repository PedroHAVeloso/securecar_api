<?php

namespace App\Repository;

use App\Utils\ErrorReport;
use PDO;
use Exception;

/**
 * Manipula o usuário no banco de dados.
 */
class UserAccountRepository extends Database
{
  public static function loginUser(string $email, string $password): array
  {
    try {
      $userExists = self::checkUserExists($email);
      if (!$userExists['exists']) {

        return $userExists;
      }

      $userValidated = self::checkUserValidated($email);
      if (!$userValidated['validated']) {

        return $userValidated;
      }

      $script =
        'SELECT name, birth, cpf FROM ' . self::USER_TABLE .
        ' WHERE email = :email AND password = :password;';

      $query = self::$connection->prepare($script);

      $query->bindValue(':email', $email);
      $query->bindValue(':password', $password);

      $query->execute();

      if ($query->rowCount() > 0) {
        $response = $query->fetch(PDO::FETCH_ASSOC);
        $sessionToken = UserSessionRepository::createSession($jsonData);

        $response =
          [
            'status' => 200,
            'login' => true,
            'session_token' => $sessionToken,
            'user' => $response
          ];

        return $response;
      } else{
        
      }

    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    }
  }

  public static function registerUser(object $jsonData)
  {
    try {
      $connection = self::connect();

      $script = 'SELECT email FROM ' . self::USER_TABLE . ' WHERE email = :email;';

      $query = $connection->prepare($script);
      $query->bindValue(':email', $jsonData->email);
      $query->execute();

      if ($query->rowCount() > 0) {
        return [
          'status' => 200,
          'register' => false,
          'reason' => 'USER ALREADY EXISTS'
        ];
      } else {
        $script =
          'INSERT INTO ' . self::USER_TABLE .
          ' (name, birth, cpf, email, password)
          VALUES (:name, :birth, :cpf, :email, :password);';

        $query = $connection->prepare($script);

        $query->bindValue(':name', $jsonData->name);
        $query->bindValue(':birth', $jsonData->birth);
        $query->bindValue(':cpf', $jsonData->cpf);
        $query->bindValue(':email', $jsonData->email);
        $query->bindValue(':password', $jsonData->password);

        $query->execute();

        $script =
          'INSERT INTO ' . self::USER_VALIDATION_TABLE .
          ' (validation_code, user_id) ' .
          'VALUES (:validation_code, (SELECT id FROM ' . self::USER_TABLE . ' WHERE email = :email));';

        $query = $connection->prepare($script);

        $query->bindValue(':validation_code', $jsonData->validation_code);
        $query->bindValue(':email', $jsonData->email);

        $query->execute();

        $sessionToken = UserSession::createSession($jsonData);

        return [
          'status' => 200,
          'register' => true,
          'session_token' => $sessionToken
        ];
      }
    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    } finally {
      self::close($connection);
    }
  }


  public static function validateUser(object $jsonData)
  {
    try {
      $connection = self::connect();

      $script = '
        SELECT is_validated FROM ' . self::USER_TABLE .
        ' WHERE email = :email;
        ';
      $query = $connection->prepare($script);

      $query->bindValue(':email', $jsonData->email);

      $query->execute();

      if ($query->rowCount() > 0) {
        $response = $query->fetch(PDO::FETCH_ASSOC);
        if ($response['is_validated'] == 1) {
          $script = '
            SELECT validation_code FROM ' . self::USER_VALIDATION_TABLE .
            ' WHERE user_id = (SELECT id FROM ' . self::USER_TABLE . ' WHERE email = :email);
            ';

          $query = $connection->prepare($script);
          $query->bindValue(':email', $jsonData->email);
          $query->execute();
          $response = $query->fetch(PDO::FETCH_ASSOC);
          if ($response['validation_code'] == $jsonData->validation_code) {

            $script = 'UPDATE ' . self::USER_TABLE . ' SET is_validated = 2 WHERE email = :email;';
            $query = $connection->prepare($script);

            $query->bindValue(':email', $jsonData->email);

            $query->execute();
            return [
              'status' => 200,
              'validate' => true
            ];
          } else {
            return [
              'status' => 200,
              'validate' => false,
              'reason' => 'INVALID VALIDATION CODE'
            ];
          }
        } else {
          return [
            'status' => 200,
            'validate' => false,
            'reason' => 'USER ALREADY VALIDATED'
          ];
        }
      } else {
        return [
          'status' => 200,
          'validate' => false,
          'reason' => 'USER NOT FOUND'
        ];
      }
    } catch (Exception $exc) {
      ServerError::addError($exc->getMessage());
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    } finally {
      self::close($connection);
    }
  }

  public static function checkUserValidated(string $email): array
  {
    try {
      if (self::checkUserExists($email)['exists']) {
        $script =
          'SELECT is_validated FROM ' . self::USER_TABLE .
          ' WHERE email = :email;';

        $query = self::$connection->prepare($script);

        $query->bindValue(':email', $email);

        $query->execute();

        $userValidated = $query->fetch(PDO::FETCH_ASSOC);

        if ($userValidated['is_validated']) {

          return [
            'status' => 'OK',
            'validated' => true
          ];
        } else {

          return [
            'status' => 'OK',
            'validated' => false,
            'reason' => 'USER NOT VALIDATED'
          ];
        }
      } else {

        return [
          'status' => 'OK',
          'validated' => false,
          'reason' => 'USER NOT FOUND'
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

  public static function checkUserExists(string $email): array
  {
    try {
      $script =
        'SELECT email FROM ' . self::USER_TABLE .
        ' WHERE email = :email;';

      $query = self::$connection->prepare($script);

      $query->bindValue(':email', $email);

      $query->execute();

      if ($query->rowCount() > 0) {

        return [
          'status' => 'OK',
          'exists' => true
        ];
      } else {

        return [
          'status' => 'OK',
          'exists' => false,
          'reason' => 'USER NOT FOUND'
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
}