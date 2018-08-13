<?php 

use \Matheushmg\Page;
use \Matheushmg\Model\Product;
use \Matheushmg\Model\Category;

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


$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>Product::checkList($category->getProducts())
	]);

});

?>