<?php

namespace App\Utils;

class JsonData
{
  public static function getJsonData()
  {
    $jsonData = json_decode(file_get_contents('php://input'));

    return $jsonData;
  }

  public static function checkDataExistence(?object $jsonData, array $data)
  {
    $return = true;

    foreach ($data as $value) {
      if (!isset($jsonData->$value)) {
        $return = false;
      }
    }

    return $return;
  }
}