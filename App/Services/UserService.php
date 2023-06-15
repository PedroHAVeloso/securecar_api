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
  public static function registerUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkRegisterData($jsonData)) {
      echo json_encode(UserAccount::registerUser($jsonData));

    }
  }

  public static function loginUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkLoginData($jsonData)) {
      echo json_encode(UserAccount::loginUser($jsonData));
    }
  }


  public static function checkUserSessionValidate()
  {
    $jsonData = JsonData::getJsonData();
    if (UserController::checkSessionValidateData($jsonData)) {
      echo json_encode(UserSession::checkSessionValidate($jsonData));
    }
  }


  public static function validateUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkValidateUserData($jsonData)) {
      echo json_encode(UserAccount::validateUser($jsonData));
    } else {
      ErrorReport::displayErrorToUser(400, 'INCORRECT DATA');
    }
  }

  public static function closeSession()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkCloseSessionData($jsonData)) {
      echo json_encode(UserSession::closeSession($jsonData));

    } else {
      ErrorReport::displayErrorToUser(400, 'INCORRECT DATA');
    }
  }
}