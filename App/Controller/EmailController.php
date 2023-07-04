<?php

namespace App\Controller;

use App\Utils\JsonData;

/**
 * Controla os dados para as respectivas funções da API.
 * 
 * TODO: A verificação dos dados precisa ser implementada.
 */
class EmailController
{
  public static function checkSendUserCodeData(?object $jsonData)
  {
    if (
      JsonData::checkDataExistence(
        $jsonData,
        [
          'email',
          'name',
          'code',
        ],
      )
    ) {
      return true;
    } else {
      return false;
    }
  }
}