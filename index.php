<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Matheushmg\Page;
use \Matheushmg\PageAdmin;

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

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->run();

 ?>

