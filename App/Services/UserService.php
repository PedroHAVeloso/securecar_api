<?php

namespace App\Services;

use App\Controller\UserController;
use App\Repository\User;
use App\Repository\UserAccount;
use App\Repository\UserSession;
use App\Utils\ErrorReport;
use App\Utils\JsonData;

class UserService
{
  public static function post()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkRegisterData($jsonData)) {
      echo json_encode(UserAccount::registerUser($jsonData));

    } elseif (UserController::checkLoginData($jsonData)) {
      echo json_encode(UserAccount::loginUser($jsonData));

    } elseif (UserController::checkSessionValidateData($jsonData)) {
      echo json_encode(UserSession::checkSessionValidate($jsonData));

    } else {
      ErrorReport::displayErrorToUser(400, 'INCORRECT DATA');
    }
  }

  public static function put()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkValidateUserData($jsonData)) {
      echo json_encode(UserAccount::validateUser($jsonData));

    } else {
      ErrorReport::displayErrorToUser(400, 'INCORRECT DATA');
    }
  }

  public static function delete()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkCloseSessionData($jsonData)) {
      echo json_encode(UserSession::closeSession($jsonData));

    } else {
      ErrorReport::displayErrorToUser(400, 'INCORRECT DATA');
    }
  }
}