<?php

namespace App\Routes;

use App\Utils\RequestMethod;
use App\Utils\RequestUri;

class Route
{
  /**
   * Função para a crição das rotas.
   * 
   * Verifica se a rota atual corresponde à rota da função.
   * 
   * @param string $method
   * @param string $route
   * @param mixed $function
   * @return void
   */
  public static function createRoute(string $method, string $route, $function)
  {
    if (
      RequestUri::getRequestUri() == "/securecar_api$route" &&
      RequestMethod::getRequestMethod() == $method
    ) {
      $function();
    }
  }
}