<?php
/**
 * Created by PhpStorm.
 * User: rydkey
 * Date: 07/11/16
 * Time: 11:17
 */

namespace App\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\ProduitModel;
use App\Model\TypeProduitModel;
use App\Model\PanierModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class PanierController implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */

    private $produitModel;
    private $panierModel;

    /**
     * @param Application $app
     * @return mixed
     */
    public function showPanier(Application $app){
        $this->panierModel = new PanierModel($app);
        $this->produitModel = new ProduitModel($app);
        $produitsPanier = $this->panierModel->getUserLigneCommande($app['session']->get('idUser'));
        $produits = $this->produitModel->getAllProduits();
        return $app["twig"]->render('frontOff/ProduitPanier/show.html.twig',['data'=>$produits, 'panier'=> $produitsPanier]);
    }

    /**
     * @param Application $app
     */
    public function addPanier(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $this->produitModel= new ProduitModel($app);
        $donnees=$this->produitModel->getProduit($id);
        $quantite=$this->panierModel->getQuantite($id,$app['session']->get('idUser'));
        if((int)$quantite==null){
            $this->panierModel->addLigneCommande($donnees,$app['session']->get('idUser'));
        }else{
            $this->panierModel->incrementQuantite($id,$app['session']->get('idUser'));
        }
        return $this->showPanier($app);
    }

    public function deletePanier(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $quantite=$this->panierModel->getQuantite($id,$app['session']->get('idUser'));
        $this->panierModel->decrementQuantite($id,$app['session']->get('idUser'));
        if((int)$quantite>1){
            $this->panierModel->decrementQuantite($id,$app['session']->get('idUser'));
        }else{
            $this->panierModel->deleteLigneCommande($id);
        }
        return $this->showPanier($app);
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];


        $controllers->get('/show', 'App\Controller\PanierController::showPanier')->bind('panier.show');
        $controllers->get('/add/{id}', 'App\Controller\PanierController::addPanier')->bind('panier.add')->assert('id', '\d+');
        $controllers->get('/delete/{id}', 'App\Controller\PanierController::deletePanier')->bind('panier.delete')->assert('id', '\d+');


        return $controllers;
    }
}