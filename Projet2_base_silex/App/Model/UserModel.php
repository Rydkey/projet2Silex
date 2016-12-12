<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class UserModel {

	private $db;

	public function __construct(Application $app) {
		$this->db = $app['db'];
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT login,password,droit,id FROM users WHERE login = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}

	public function getUser($id){
		$queryBuilder= new QueryBuilder($this->db);
        $queryBuilder
            ->select('nom','adresse','ville','code_postal','email')
            ->from('users')
            ->where('id=?')
            ->setParameter(0,$id);
        return $queryBuilder->execute()->fetch();
	}

	public function updateUser($id,$donnees){
		$queryBuilder= new QueryBuilder($this->db);
        $queryBuilder
            ->update('users')
            ->set('nom', '?')
            ->set('adresse','?')
            ->set('ville','?')
            ->set('code_postal','?')
            ->set('email','?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['adresse'])
            ->setParameter(2, $donnees['ville'])
            ->setParameter(3, $donnees['code_postal'])
            ->setParameter(4, $donnees['email'])
            ->setParameter(5, $id);
        return $queryBuilder->execute();

	}
}