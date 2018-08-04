<?php 

namespace Matheushmg;

class PageAdmin extends Page {

	public function __construct($opts = array(), $tpl_dir = "/views/admin/") {

		parent::__construct($opts, $tpl_dir); // Essa Função faz uma copia da classe PAI(Page).

	}

}

?>