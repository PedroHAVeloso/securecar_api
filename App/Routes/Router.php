<?php

namespace App\Routes;

use App\Repository\ApiToken;
use App\Services\UserService;
use App\Utils\ErrorReport;
use App\Utils\HttpHeaders;
use App\Utils\RequestMethod;

class Router extends Route
{
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

  public static function defineRoute()
  {
    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/register',
      function () {
        // TODO
      }
    );

    self::createRoute(
      RequestMethod::METHOD_PUT,
      '/user/validate-user',
      function () {
        // TODO
      }
    );

    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/login',
      function () {
        // TODO: 
      }
    );

    self::createRoute(
      RequestMethod::METHOD_POST,
      '/user/login',
      function () {
        UserService::post();
      }
    );
  }

  public static function ignoreOptionRequest(): bool
  {
    if (RequestMethod::getRequestMethod() == RequestMethod::METHOD_OPTIONS) {
      return false;
    } else {
      return true;
    }
  }

  public static function validateEntry(): bool|array
  {
    $authorization = HttpHeaders::getAuthorization();
    $authorization = ApiToken::checkApiTokenValidity($authorization);

    if ($authorization == 200) {
      return true;
    } else if ($authorization == 400) {
      return ['status' => 'API TOKEN IS INVALID', 'status_code' => $authorization];
    } else {
      return ['status' => 'SERVER ERROR', 'status_code' => $authorization];
    }
  }
}