<?php

namespace App\Routes;

use App\Repository\ApiTokenRepository;
use App\Services\UserService;
use App\Utils\HttpHeaders;
use App\Utils\RequestMethod;

class Router extends Route
{
  /**
   * Inicia a conexão com a API.
   */
  public static function enterApi()
  {
    if (self::ignoreOptionRequest()) {
      if ($validated = self::validateEntry()) {
        self::defineRoute();
      } else {
        echo json_encode($validated);
      }
    }
  }

  /**
   * Define a rota a partir da URI atual.
   * @return void
   */
  public static function defineRoute()
  {
    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/register',
      function () {
        UserService::registerUser();
      }
    );

    self::createRoute(
      RequestMethod::METHOD_PUT,
      '/user/validate-user',
      function () {
        UserService::validateUser();
      }
    );

    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/login',
      function () {
        UserService::loginUser();
      }
    );

    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/check-session-validity',
      function () {
        UserService::checkUserSessionValidity();
      }
    );

    self::createRoute(
      RequestMethod::METHOD_DELETE,
      '/user/close-session',
      function () {
        UserService::closeSession();
      }
    );
  }

  /**
   * Ignora o Request Option, evitando erros
   * durante requisições.
   * @return bool
   */
  public static function ignoreOptionRequest(): bool
  {
    if (RequestMethod::getRequestMethod() == RequestMethod::METHOD_OPTIONS) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Valida o a authorization informado na requisição para uso
   * da API.
   * @return bool|array
   */
  public static function validateEntry(): bool|array
  {
    $authorization = HttpHeaders::getAuthorization();
    $authorization = ApiTokenRepository::checkApiTokenValidity($authorization);

    if ($authorization == 200) {
      return true;
    } else if ($authorization == 400) {
      return ['status' => 'API TOKEN IS INVALID', 'status_code' => $authorization];
    } else {
      return ['status' => 'SERVER ERROR', 'status_code' => $authorization];
    }
  }
}