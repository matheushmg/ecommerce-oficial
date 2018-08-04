<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Matheushmg\Page;
use \Matheushmg\PageAdmin;
use \Matheushmg\Model\User;


$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // São as Rotas; Função onde os arquivos poderão ser encontrados.
    /*
	$sql = new Matheushmg\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results);
	*/ // Testando o Banco de dados..

	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function() { 

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function() { 

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() { 

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function() { 

	User::logout();

	header("Location: /admin/login");
	exit;

});

$app->run();

 ?>

