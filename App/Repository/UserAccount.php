<?php

namespace App\Repository;

use App\Utils\ErrorReport;
use PDO;
use Exception;

class UserAccount extends Database
{
  public static function loginUser(object $jsonData)
  {
    try {
      $connection = self::connect();

      $script =
        'SELECT name, birth, cpf, is_validated FROM ' . self::USER_TABLE .
        ' WHERE email = :email AND password = :password;';

      $query = $connection->prepare($script);

      $query->bindValue(':email', $jsonData->email);
      $query->bindValue(':password', $jsonData->password);

      $query->execute();

      if ($query->rowCount() > 0) {
        $response = $query->fetch(PDO::FETCH_ASSOC);

        if ($response['is_validated'] == 1) {
          return [
            'status' => 200,
            'login' => false,
            'reason' => 'USER NOT VALIDATED'
          ];
        } else {
          $sessionToken = UserSession::createSession($jsonData);

          $response =
            [
              'status' => 200,
              'login' => true,
              'session_token' => $sessionToken,
              'user' => $response
            ];

          return $response;
        }
      } else {
        return [
          'status' => 200,
          'login' => false,
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
}