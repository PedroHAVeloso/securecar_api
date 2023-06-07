<?php

require_once 'vendor/autoload.php';

use App\Routes\Routes;

header('Content-Type: application/json; charset=utf-8');

Routes::enterApi();

?>