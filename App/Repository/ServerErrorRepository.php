<?php

namespace App\Repository;

use Exception;

/**
 * Manipula as mensagens de erro do servidor.
 */
class ServerErrorRepository extends Database
{
  /**
   * Adiciona uma mensagem de erro no banco de dados
   * para registro.
   * 
   * Retorna um [bool] informando a situação da inserção.
   * 
   * @param string $error
   * @return bool
   */
  public static function addError(string $error)
  {
    try {
      $script =
        'INSERT INTO ' . self::ERROR_TABLE .
        ' (error) VALUES (:error);';

      $query = self::$connection->prepare($script);
      $query->bindValue(':error', $error);

      $query->execute();

      return true;
    } catch (Exception $exc) {

      return false;
    }
  }
}