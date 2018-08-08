<?php 

namespace Matheushmg\Model;

use \Matheushmg\DB\Sql;
use \Matheushmg\Model;
use \Matheushmg\Mailer;
use \Matheushmg\Model\User;

class Rp extends Model{

      const SESSION = "User";
      const SECRET = "FontePhp7_Secret";

      public static function getForgot($email, $inadmin = true) {
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email",array(
                  ":email"=>$email
            ));
            
            if (count($results) === 0) {
                  throw new \Exception("Não foi possível recuperar a senha.");
            } else {

                  $data = $results[0];

                  $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                        ":iduser"=>$data["iduser"],
                        ":desip"=>$_SERVER["REMOTE_ADDR"]
                  ));
                  
                  if(count($results2) === 0){
                        throw new \Exception("Não foi possível recuperar a senha.");
                  } else {

                        $dataRecovery = $results2[0];

                        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));

                        $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);

                        $result = base64_encode($iv.$code);

                        if ($inadmin === true) {
                              $link = "http://www.lojavirtual.com/admin/forgot/reset?code=$result";
                        } else {
                              $link = "http://www.lojavirtual.com/forgot/reset?code=$resultt";
                        } 

                        $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
                             "name"=>$data['desperson'],
                             "link"=>$link
                        )); 

                        $mailer->send();

                        return $link;
                  }
            }
      }

      public static function validForgotDecrypt($result) {

           $result = base64_decode($result);

           $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');

           $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');

           $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
           
           $sql = new Sql();

           $results = $sql->select("
               SELECT *
               FROM tb_userspasswordsrecoveries a
               INNER JOIN tb_users b USING(iduser)
               INNER JOIN tb_persons c USING(idperson)
               WHERE
               a.idrecovery = :idrecovery
               AND
               a.dtrecovery IS NULL
               AND
               DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
           ", array(
               ":idrecovery"=>$idrecovery
           ));
           if (count($results) === 0)
           {
               throw new \Exception("Não foi possível recuperar a senha.");
           }
           else
           {
               return $results[0];
           }
      }
      public static function setForgotUsed($idrecovery)
      {
            $sql = new Sql();

            $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery =NOW() WHERE idrecovery = :idrecovery", array(
                  ":idrecovery" => $idrecovery
            ));
      }

      public function setPassword($password)
      {
            $sql = new Sql();

            $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser",array(
                  ":password" => $password,
                  ":iduser"=>$this->getiduser()
            ));
      }
}

 ?>