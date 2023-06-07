<?php

namespace App\Utils;

class GenerateAleatoryString
{
  public static function generateAlphaNumeric(int $maxCharacters)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $maxCharacters; $i++) {
      $index = rand(0, strlen($characters) - 1);
      $string .= $characters[$index];
    }

    return $string;
  }
}