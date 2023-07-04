<?php

namespace App\Services;

use App\Controller\EmailController;
use App\Repository\ServerErrorRepository;
use App\Utils\Env;
use App\Utils\JsonData;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
  public static function sendUserCode()
  {
    $jsonData = JsonData::getJsonData();

    if (EmailController::checkSendUserCodeData($jsonData)) {
      $send = self::sendEmail(
        $jsonData->email,
        $jsonData->name,
        "Código de validação",
        "O seu código de validação é: " . $jsonData->code,
      );

      if ($send) {
        echo json_encode(array('status' => 'OK'));
      } else {
        http_response_code(500);

        echo json_encode(
          array('status' => 'SERVER ERROR')
        );
      }

    } else {
      http_response_code(400);

      echo json_encode(
        array('status' => 'DATA ERROR')
      );
    }
  }

  private static function sendEmail(
    string $address,
    string $name,
    string $subject,
    string $body,
  ) {
    $env = Env::getEnv();

    $mail = new PHPMailer();

    $mail->isSMTP();

    // $mail->SMTPDebug = 2;
    
    $mail->SMTPAuth = true;
    $mail->CharSet = "UTF-8";

    $mail->Host = $env["MAIL_HOST"];
    $mail->Port = $env["MAIL_PORT"];
    $mail->Username = $env["MAIL_USERNAME"];
    $mail->Password = $env["MAIL_PASSWORD"];
    $mail->setFrom($env["MAIL_USERNAME"], $env["MAIL_NAME"]);

    $mail->addAddress($address, $name);
    $mail->Subject = $subject;

    // $mail->msgHTML(file_get_contents('message.html'), __DIR__);

    $mail->Body = $body;

    if (!$mail->send()) {
      ServerErrorRepository::addError($mail->ErrorInfo);
      return false;
    }

    return true;
  }

}