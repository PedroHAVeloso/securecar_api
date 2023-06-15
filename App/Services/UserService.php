<?php

namespace App\Services;

use App\Controller\UserController;
use App\Repository\UserAccountRepository;
use App\Repository\UserSessionRepository;
use App\Utils\ErrorReport;
use App\Utils\JsonData;

class UserService
{
  public static function registerUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkRegisterData($jsonData)) {

      echo json_encode(
        UserAccountRepository::registerUser(
          $jsonData->name,
          $jsonData->birth,
          $jsonData->cpf,
          $jsonData->email,
          $jsonData->password,
          $jsonData->validation_code
        )
      );
    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }

  public static function loginUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkLoginData($jsonData)) {

      echo json_encode(
        UserAccountRepository::loginUser(
          $jsonData->email,
          $jsonData->password
        )
      );
    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }


  public static function checkUserSessionValidity()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkSessionValidateData($jsonData)) {

      echo json_encode(
        UserSessionRepository::checkSessionValidate(
          $jsonData->session_token
        )
      );
    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }

  public static function validateUser()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkValidateUserData($jsonData)) {

      echo json_encode(
        UserAccountRepository::validateUser(
          $jsonData->email,
          $jsonData->validation_code
        )
      );
    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }

  public static function closeSession()
  {
    $jsonData = JsonData::getJsonData();

    if (UserController::checkCloseSessionData($jsonData)) {

      echo json_encode(
        UserSessionRepository::closeSession(
          $jsonData->session_token
        )
      );
    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }
}