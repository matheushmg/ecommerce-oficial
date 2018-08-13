<?php 

use \Matheushmg\Page;
use \Matheushmg\Model\Product;

$app->get('/', function() { // São as Rotas; Função onde os arquivos poderão ser encontrados.
    /*
	$sql = new Matheushmg\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results);
	*/ // Testando o Banco de dados..

	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", [
		"products"=>Product::checkList($products)
	]);

});

?>