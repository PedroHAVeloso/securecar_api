<?php

namespace App\Repository;

use App\Utils\Env;
use Exception;
use PDO;

/**
 * Manipula a conexão com o banco de dados.
 */
class Database
{
  // TABELAS DO BANCO
  protected const USER_TABLE = 'tb_user';
  protected const USER_SESSIONS_TABLE = 'tb_user_sessions';
  protected const API_TOKEN_TABLE = 'tb_api_token';
  protected const USER_VALIDATION_TABLE = 'tb_user_validation';
  protected const ERROR_TABLE = 'tb_server_error_log';

  // CONEXÃO
  protected static PDO|null $connection;

  public function __construct()
  {
    self::$connection = self::connect();
  }

  public function __destruct()
  {
    self::close($connection);
  }

  /**
   * Inicia uma conexão com o banco de dados.
   * 
   * As informações para a conexão devem estar em um
   * arquivo .env na raiz do projeto.
   * 
   * Retorna um objeto [PDO] ou [null] em caso de erro.
   * 
   * @return PDO|null
   */
  protected static function connect(): PDO|null
  {
    try {
      $env = Env::getEnv();

      $connection = new PDO(
        $env['DB_DRIVE']
        . ':dbname=' . $env['DB_NAME']
        . ';host=' . $env['DB_HOST'],
        $env['DB_USER'],
        $env['DB_PASS'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
      );

      return $connection;
    } catch (Exception $exception) {

      throw $exception;
    }
  }

  /**
   * Destrói a conexão com o banco de dados.
   * 
   * @param PDO $connection
   * @return null
   */
  protected static function close(&$connection): null
  {
    $connection = null;
    return $connection;
  }
}