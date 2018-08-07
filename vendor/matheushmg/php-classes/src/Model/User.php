<?php 

namespace Matheushmg\Model;

use \Matheushmg\DB\Sql;
use \Matheushmg\Model;
use \Matheushmg\Mailer;
use Rain\Tpl\Exception;


class User extends Model{

	const SESSION = "User";
	const SECRET = "SequenciaDeCaracteres";

	public static function login($login, $password)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if(count($results) === 0){
			throw new \Exception("Usuario Inexistente ou Senha Invalida.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"]) === true){

			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION]  = $user->getValues();

			return $user;

			/*var_dump($user); // Quando for retornar um array user var_dump
			exit;*/

		} else {
			throw new \Exception("Usuario Inexistente ou Senha Inválida!!");
			//echo $login;
		}

	}

	public static function verifyLogin( $inadmin = true){

		if(
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		) {
			header("Location: /admin/login");
			exit;
		}	

	}

	public static function logout(){

		$_SESSION[User::SESSION] = NULL;

	}

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	public function get($iduser){ // Parte Para o funcionamento da Etapa de Edita Usuarios
	
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(":iduser"=>$iduser));

		$data = $results[0];

		$this->setData($data);

	}

	public function save() { // Salva as criações de Usuarios par o banco de dados
		
		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	
	}

	public function update() {

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	
	}

	public function delete() {
		
		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));
	}

	public static function getForgot($email, $inadmin = true){

	        $sql = new Sql();

	        $res = $sql->select("
	            
	            SELECT * 
	            FROM tb_persons a 
	            INNER JOIN tb_users b 
	            USING (idperson) 
	            WHERE a.desemail = :email;",
	            array(":email" => $email)
	        );

	        if (count($res) === 0) {
	            throw new \Exception("Não foi possível recuperar a senha.");
	        } else {

	            $data = $res[0];

	            $results = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
	                ":iduser" => $data["iduser"],
	                ":desip" => $_SERVER["REMOTE_ADDR"]
	            ));

	            if (count($results) === 0) {
	                throw new \Exception("Não foi possível recuperar a senha.");
	            } else {

	                $dataRecovery   = $results[0];
	                $iv             = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	                $code           = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
	                $result         = base64_encode($iv . $code);
	                if ($inadmin === true) {
	                    $link = "http://www.lojavirtual.com/admin/forgot/reset?code=$result";
	                } else {
	                    $link = "http://www.lojavirtual.com/forgot/reset?code=$result";
	                }

	                $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha", "forgot",
	                    array(
	                        "name" => $data["desperson"],
	                        "link" => $link
	                    )
	                );

	                $mailer->send();

	                return $link;
	            }
	        }
	    }

	}


?>