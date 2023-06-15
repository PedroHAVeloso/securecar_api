<?php

namespace App\Controller;

use App\Utils\JsonData;

/**
 * Controla os dados para as respectivas funções da API.
 * 
 * TODO: A verificação dos dados precisa ser implementada.
 */
class UserController
{
  public static function checkLoginData(?object $jsonData)
  {
    if (
      JsonData::checkDataExistence(
        $jsonData,
        [
          'email',
          'password',
        ],
      )
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function checkSessionValidateData(?object $jsonData)
  {
    return JsonData::checkDataExistence($jsonData, ['session_token']);
  }

  public static function checkRegisterData(?object $jsonData)
  {
    if (
      JsonData::checkDataExistence(
        $jsonData,
        [
          'name',
          'email',
          'cpf',
          'birth',
          'password',
          'validation_code'
        ],
      )
    ) {
      return true;
    } else {
      return false;
    }
  }

  public static function checkValidateUserData(?object $jsonData)
  {
    if (
      JsonData::checkDataExistence(
        $jsonData,
        [
          'email',
          'validation_code',
        ],
      )
    ) {
      return true;
    } else {
      return false;
    }
  }


  public static function checkCloseSessionData(?object $jsonData)
  {
    if (
      JsonData::checkDataExistence(
        $jsonData,
        [
          'session_token',
        ],
      )
    ) {
      return true;
    } else {
      return false;
    }
  }
}