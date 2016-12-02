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
    public function addPanier(Application $app,Request $request){
        $stock=$request->get('stock');
        $id=$request->get('id');
        $this->panierModel = new PanierModel($app);
        $this->produitModel= new ProduitModel($app);
        $donnees=$this->produitModel->getProduit($id);
        $quantite=$this->panierModel->getQuantite($id,$app['session']->get('idUser'));
        if((int)$quantite==null){
            $this->panierModel->addLigneCommande($donnees,$app['session']->get('idUser'),$stock);
        }else{
            $this->panierModel->incrementQuantite($id,$app['session']->get('idUser'),$stock);
        }
        return $app->redirect($app["url_generator"]->generate("produit.Client"));

    }

    public function deletePanier(Application $app,Request $request){
        $id=$request->get('id');
        $delete_quantite=$request->get('delete_quantite');
        $this->panierModel = new PanierModel($app);
        $quantite=$this->panierModel->getQuantiteIdPanier($id);
        if((int)$quantite['quantite']<=$delete_quantite){
            $this->panierModel->deleteLigneCommande($id);
        }else{
           $this->panierModel->decrementQuantite($id,$delete_quantite);
        }
        return $app->redirect($app["url_generator"]->generate("produit.Client"));
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];


        $controllers->get('/Client', 'App\Controller\PanierController::showPanier')->bind('panier.Client');
        $controllers->get('/add/{id}', 'App\Controller\PanierController::addPanier')->bind('panier.add')->assert('id', '\d+');
        $controllers->post('/add/{id}', 'App\Controller\PanierController::addPanier')->bind('panier.add')->assert('id', '\d+');
        $controllers->post('/add', 'App\Controller\PanierController::addPanier')->bind('panier.add');
        $controllers->get('/delete/{id}', 'App\Controller\PanierController::deletePanier')->bind('panier.delete')->assert('id', '\d+');
        $controllers->post('/delete/{id}', 'App\Controller\PanierController::deletePanier')->bind('panier.delete')->assert('id', '\d+');
        $controllers->post('/delete', 'App\Controller\PanierController::deletePanier')->bind('panier.delete');


        return $controllers;
    }
}