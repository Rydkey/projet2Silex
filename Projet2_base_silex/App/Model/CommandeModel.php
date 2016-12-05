<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\SQLSrv\SQLSrvConnection;
use Silex\Application;

//bite

class CommandeModel {
    
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

//CREATE TABLE IF NOT EXISTS commandes (
//id int(11) NOT NULL AUTO_INCREMENT,
//user_id int(11) NOT NULL,
//prix float(6,2) NOT NULL,
//date_achat  timestamp default CURRENT_TIMESTAMP,
//etat_id int(11) NOT NULL,
//PRIMARY KEY (id),
//CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
//CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
//) DEFAULT CHARSET=utf8 ;




    public function CreateCommand($idUser,$prix){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commandes')
            ->values([
                'user_id' => '?',
                'prix' => '?',
                'etat_id' => '?',
            ])
            ->setParameter(0, $idUser)
            ->setParameter(1,$prix)
            ->setParameter(2, 1)
        ;
        $queryBuilder->execute();
        $queryBuilder = new QueryBuilder($this->db);
        $id_commande=$this->db->lastInsertId();
        $queryBuilder->update('paniers')
            ->set('commande_id','?')
            ->where('user_id= ?',$queryBuilder->expr()->isNull('commande_id'))
            ->setParameter(0,$id_commande)
            ->setParameter(1,$idUser);
        return $queryBuilder->execute();
    }

    /**
     * @param $produits
     * @return int
     */
    public function PrixTotal($produits){
        $prixTotal=0;
        foreach ($produits as $result){
            $prixTotal+=$result['prix']*$result['quantite'];
        }
        return $prixTotal;
    }

    public function ShowCommand($idUser)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id','e.libelle')
            ->from('commandes', 'c')
            ->where('c.user_id=:id')
            ->innerJoin('c' ,'etats' ,'e', 'c.etat_id=e.id')
            ->setParameter('id',(int)$idUser)
            ->addOrderBy('c.id', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }
}
