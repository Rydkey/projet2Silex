<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
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
}
