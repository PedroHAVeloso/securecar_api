<?php

namespace App\Repository;

use PDO;
use Exception;

/**
 * Manipula o usuário no banco de dados.
 */
class UserAccountRepository extends Database
{

  /**
   * Inicia sessão de usuários existentes no banco.
   * 
   * Retorna um array com a resposta do login.
   * 
   * @param string $email
   * @param string $password
   * @return array
   */
  public static function loginUser(string $email, string $password): array
  {
    try {
      $userExists = self::checkUserExists($email);
      if (!$userExists['exist']) {

        return [
          'status' => 'OK',
          'login' => false,
          'reason' => 'USER NOT FOUND'
        ];
      }

      $userValidated = self::checkUserValidated($email);
      if (!$userValidated['validated']) {

        return [
          'status' => 'OK',
          'login' => false,
          'reason' => 'USER NOT VALIDATED'
        ];
      }

      $script =
        'SELECT name, birth, cpf, is_validated FROM ' . self::USER_TABLE .
        ' WHERE email = :email AND password = :password;';

      $query = self::$connection->prepare($script);

      $query->bindValue(':email', $email);
      $query->bindValue(':password', $password);

      $query->execute();

      if ($query->rowCount() > 0) {
        $response = $query->fetch(PDO::FETCH_ASSOC);

        $sessionToken = UserSessionRepository::createSession($email);

        return [
          'status' => 'OK',
          'login' => true,
          'session_token' => $sessionToken['session_token'],
          'user' => $response
        ];
      } else {

        return [
          'status' => 'OK',
          'login' => false,
          'reason' => 'INVALID EMAIL OR PASSWORD'
        ];
      }

    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);

      return [
        'status' => 'SERVER ERROR',
      ];
    }
  }

  /**
   * Registra um novo usuário.
   * 
   * @param string $name
   * @param string $birth
   * @param string $cpf
   * @param string $email
   * @param string $password
   * @param int $validationCode
   * @return array
   */
  public static function registerUser(
    string $name, string $birth,
    string $cpf, string $email,
    string $password, int $validationCode
  ) {
    try {
      $userExists = self::checkUserExists($email);
      if ($userExists['exist']) {

        return [
          'status' => 'OK',
          'register' => false,
          'reason' => 'USER ALREADY EXISTS'
        ];
      }

      $script =
        'INSERT INTO ' . self::USER_TABLE .
        ' (name, birth, cpf, email, password)
          VALUES (:name, :birth, :cpf, :email, :password);';

      $query = self::$connection->prepare($script);

      $query->bindValue(':name', $name);
      $query->bindValue(':birth', $birth);
      $query->bindValue(':cpf', $cpf);
      $query->bindValue(':email', $email);
      $query->bindValue(':password', $password);

      $query->execute();

      $script =
        'INSERT INTO ' . self::USER_VALIDATION_TABLE .
        ' (validation_code, user_id) ' .
        'VALUES (:validation_code, (SELECT id FROM ' . self::USER_TABLE . ' WHERE email = :email));';

      $query = self::$connection->prepare($script);

      $query->bindValue(':validation_code', $validationCode);
      $query->bindValue(':email', $email);

      $query->execute();

      $sessionToken = UserSessionRepository::createSession($email);

      return [
        'status' => 'OK',
        'register' => true,
        'session_token' => $sessionToken['session_token']
      ];
    } catch (Exception $exception) {
      ServerErrorRepository::addError($exception);

      return [
        'status' => 'SERVER ERROR'
      ];
    }
  }

  /**
   * Valida a conta do usuário.
   * 
   * Recebe o email e o código de validação do usuário para validar 
   * sua conta.
   * 
   * Retorna um array com o campo booleano [validate].
   * 
   * @param string $email
   * @param string $validationCode
   * @return array
   */
  public static function validateUser(string $email, string $validationCode)
  {
    try {
      $userExists = self::checkUserExists($email);
      if (!$userExists['exist']) {

        return [
          'status' => 'OK',
          'validate' => false,
          'reason' => 'USER NOT FOUND'
        ];
      }

      $userValidated = self::checkUserValidated($email);
      if ($userValidated['validated']) {

        return [
          'status' => 'OK',
          'validate' => false,
          'reason' => 'USER VALIDATED'
        ];
      }

      $script = '
            SELECT validation_code FROM ' . self::USER_VALIDATION_TABLE .
        ' WHERE user_id = (SELECT id FROM ' . self::USER_TABLE . ' WHERE email = :email);
            ';

      $query = self::$connection->prepare($script);

      $query->bindValue(':email', $email);

      $query->execute();

      $response = $query->fetch(PDO::FETCH_ASSOC);

      if ($response['validation_code'] == $validationCode) {
        $script = 'UPDATE ' . self::USER_TABLE . ' SET is_validated = 2 WHERE email = :email;';

        $query = self::$connection->prepare($script);

        $query->bindValue(':email', $email);

        $query->execute();

        return [
          'status' => 'OK',
          'validate' => true
        ];
      } else {

        return [
          'status' => 'OK',
          'validate' => false,
          'reason' => 'INVALID VALIDATION CODE'
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
   * Verifica se o usuário já foi validado ou não.
   * 
   * Retorna um array com o campo booleano [validated].
   * 
   * @param string $email
   * @return array
   */
  public static function checkUserValidated(string $email): array
  {
    try {
      if (self::checkUserExists($email)['exist']) {
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

  /**
   * Realiza a verificação de existência de um usuário
   * no banco de dados.
   * 
   * Retorna um array com o campo booleano [exist].
   * 
   * @param string $email
   * @return array
   */
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
          'exist' => true
        ];
      } else {

        return [
          'status' => 'OK',
          'exist' => false,
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