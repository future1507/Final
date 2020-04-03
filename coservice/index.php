<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath("/coservice");

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/api/user.php';
require __DIR__ . '/api/document.php';
require __DIR__ . '/api/filecabinet.php';
require __DIR__ . '/api/file.php';
require __DIR__ . '/api/covid.php';

$app->run();