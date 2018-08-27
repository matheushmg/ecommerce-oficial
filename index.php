<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once ("rotas/site.php");
require_once ("rotas/function.php");
require_once ("rotas/admin.php");
require_once ("rotas/admin-users.php");
require_once ("rotas/admin-categories.php");
require_once ("rotas/admin-products.php");
require_once ("rotas/admin-orders.php");

$app->run();

 ?>

