<?php

require_once 'vendor/autoload.php';

use App\Routes\Router;

// ! Temporário
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: false");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Expose-Headers: *");

Router::enterApi();

?>