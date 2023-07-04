<?php

namespace App\Routes;

use App\Repository\ApiTokenRepository;
use App\Services\UserService;
use App\Utils\HttpHeaders;
use App\Utils\RequestMethod;
use App\Services\EmailService;

class Router extends Route
{
  /**
   * Inicia a conexão com a API.
   */
  public static function enterApi()
  {
    if (self::ignoreOptionRequest()) {
      $validated = self::validateEntry();

      if ($validated === true) {
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
      '/user/validate',
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

    self::createRoute(
      RequestMethod::METHOD_POST,
      '/email/send-user-code',
      function () {
        EmailService::sendUserCode();
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
   * Valida a authorization informado na requisição para uso
   * da API.
   * @return bool|array
   */
  public static function validateEntry(): bool|array
  {
    $authorization = HttpHeaders::getAuthorization();

    $apiTokenRepository = new ApiTokenRepository;

    $authorization = $apiTokenRepository::checkApiTokenValidity($authorization);

    if ($authorization) {

      return true;
    } else if (!$authorization) {

      return ['status' => 'API TOKEN IS INVALID', 'status_code' => 400];
    } else {

      return ['status' => 'SERVER ERROR', 'status_code' => 500];
    }
  }
}