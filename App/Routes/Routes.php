<?php

namespace App\Routes;

use App\Repository\ApiToken;
use App\Services\UserService;
use App\Utils\ErrorReport;
use App\Utils\HttpHeaders;

class Routes
{
  public static function enterApi()
  {
    if (Routes::validateEntry()) {
      Routes::defineRoute();
    }
  }

  public static function defineRoute()
  {

    Route::post('/user', function () {
      UserService::post();
    });

    Route::put('/user', function () {
      UserService::put();
    });

    Route::delete('/user', function () {
      UserService::delete();
    });
  }

  public static function validateEntry()
  {
    $authorization = HttpHeaders::getAuthorization();
    $authorization = ApiToken::checkApiTokenValidity($authorization);

    if ($authorization == 200) {
      return true;
    } else if ($authorization == 400) {
      ErrorReport::displayErrorToUser(400, 'API TOKEN IS INVALID');
    } else if ($authorization == 500) {
      ErrorReport::displayErrorToUser(500, 'SERVER ERROR');
    }
  }
}