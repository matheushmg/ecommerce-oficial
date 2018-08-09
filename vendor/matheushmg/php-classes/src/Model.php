<?php 

namespace Matheushmg;

class Model {
	
	private $values = [];

	public function __call($name, $args){ // Metodo Magico; Sendo uma configuração para entender como funciona o SET e GET sem a atribuição dos atributos SET e GET; 

		$method = substr($name, 0, 3); // Primeiro verifica se é SET e GET
		$fieldName = substr($name, 3, strlen($name)); // Segundo faz a contagem do restante dos parametros..

		switch ($method) {

			case "get":
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;				
			break;

			case "set":
				$this->values[$fieldName] = $args[0];
			break;

		}

	}

	public function setData($data = array()){ // Essa Função faz uma chamada dos metodos GET e SET automaticamente

		foreach ($data as $key => $value) { 
			
			$this->{"set".$key}($value);

		}

	}

	public function getValues(){

		return $this->values;

	}

}

?>