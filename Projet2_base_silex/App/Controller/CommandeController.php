<?php
/**
 * Created by PhpStorm.
 * User: rydkey
 * Date: 28/11/16
 * Time: 11:22
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

class CommandeController implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    private $panierModel;

    public function ValidCommand(Application $app){
        $this->panierModel = new PanierModel($app);
        $produitsPanier = $this->panierModel->getUserLigneCommande($app['session']->get('idUser'));
        return $app["twig"]->render('frontOff/Commande/ValidCommand.html.twig',['panier'=>$produitsPanier]);
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/ValidCommand', 'App\Controller\CommandeController::ValidCommand')->bind('Commande.ValidCommand');

        return $controllers;
    }
}