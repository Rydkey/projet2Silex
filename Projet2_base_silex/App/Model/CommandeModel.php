<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

//bite

class PanierModel {
    
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    
    public function addCommand($produits,$idUser,$stock){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'quantite' => '?',
                'prix' => '?',
                'user_id' => '?',
                'produit_id' => '?'
            ])
            ->setParameter(0, $stock)
            ->setParameter(1, $donnees['prix'])
            ->setParameter(2, $idUser)
            ->setParameter(3, $donnees['id'])
        ;
        return $queryBuilder->execute();
    }
    
}
