<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    /**
     * @param $app
     */
    public function getAllLigneCommande(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.nom', 'p.prix','pa.quantite','p.photo','pa.id as idPanier')
            ->from('produits', 'p')
            ->innerJoin('p', 'paniers', 'pa', 'p.id=pa.produit_id')
            ->addOrderBy('p.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @return array
     */
    public function getUserLigneCommande($idUser){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.nom', 'p.prix','pa.quantite','p.photo','pa.id as idPanier')
            ->from('produits', 'p')
            ->innerJoin('p', 'paniers', 'pa', 'p.id=pa.produit_id')
            ->where('pa.user_id=:id')
            ->setParameter('id',(int)$idUser)
            ->addOrderBy('p.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param $app
     */
    public function addLigneCommande($donnees,$idUser,$stock){
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

    /**
     * @param $id
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function deleteLigneCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('id = :id')
            ->setParameter('id',(int)$id)
        ;
        return $queryBuilder->execute();
    }

    public function getQuantite($id,$idUser){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite')
            ->from('paniers')
            ->where('user_id= ?','produit_id= ?')
            ->setParameter(0,$idUser)
            ->setParameter(1,$id);
        return $queryBuilder->execute()->fetch();

    }

    /**
     * @param $id
     * @param $get
     */
    public function incrementQuantite($id, $idUser,$stock)
    {
        (int)$quantite=(int)$this->getQuantite($id,$idUser);
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite','quantite+?')
            ->where('user_id= ?','produit_id= ?')
            ->setParameter(0,$stock)
            ->setParameter(1,$idUser)
            ->setParameter(2,$id);
        return $queryBuilder->execute();
    }

    /**
     * @param $id
     * @param $idUser
     */
    public function decrementQuantite($id,$delete_quantite)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite','quantite-?')
            ->where('id= ?')
            ->setParameter(0,$delete_quantite)
            ->setParameter(1,$id);
        return $queryBuilder->execute();
    }

    public function getQuantiteIdPanier($id)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite')
            ->from('paniers')
            ->where('id= ?')
            ->setParameter(0,$id);
        return $queryBuilder->execute()->fetch();
    }
}