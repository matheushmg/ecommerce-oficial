<?php 

namespace Matheushmg;

use Rain\Tpl;

class Page {

	private $tpl;
	private $options = [];
	private $defaults = [
		"data"=>[]
	];
	
	public function __construct($opts = array(), $tpl_dir = "/views/"){

		$this->options = array_merge($this->defaults, $opts); // Essa função esta mesclando os arrays e armazenando nela "$options"

		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false 
		);

		Tpl::configure( $config );

		$this->tpl = new Tpl; // atributo

		$this->setData($this->options["data"]);

		$this->tpl->draw("header"); // Desenhar/colocar o layout na tela.. O DRAY ele espera o nome do Arquivo que quer Chamar
	}
	private function setData($data = array()){

		foreach ($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}

	}

	public function setTpl($name, $data = array(), $returnHTML = false){ // Função para TEMPLATES

		$this->setData($data);

		return $this->tpl->draw($name, $returnHTML);

	}

	public function __destruct(){// Metodos Magicos

		$this->tpl->draw("footer");
		
	}
	
}
?>