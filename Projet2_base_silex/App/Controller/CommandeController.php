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

use App\Model\PanierModel;
use App\Model\CommandeModel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation

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
    private $commandeModel;

    public function ShowCommand(Application $app){
        $this->commandeModel=new CommandeModel($app);
        if ($app['session']->get('droit')=="DROITclient") {
            $data=$this->commandeModel->ShowCommand($app['session']->get('idUser'));
            return $app["twig"]->render('frontOff/Commande/RecapCommands.html.twig', ['data' => $data]);
        }else{
            $data=$this->commandeModel->ShowAllCommand();
            return $app["twig"]->render('backoff/Commande/RecapCommands.html.twig', ['data' => $data]);
        }
    }

    public function ValidCommand(Application $app){
        $this->panierModel = new PanierModel($app);
        $produitsPanier = $this->panierModel->getUserLigneCommande($app['session']->get('idUser'));
        return $app["twig"]->render('frontOff/Commande/ValidCommand.html.twig',['panier'=>$produitsPanier]);
    }

    public function CreateCommand(Application $app){
        $this->panierModel = new PanierModel($app);
        $this->commandeModel = new CommandeModel($app);
        $produitsPanier = $this->panierModel->getUserLigneCommande($app['session']->get('idUser'));
        $PrixTotal = $this->commandeModel->PrixTotal($produitsPanier);
        $this->commandeModel->CreateCommand($app['session']->get('idUser'),$PrixTotal);
        return $app->redirect($app["url_generator"]->generate("Commande.show"));
    }

    public function EnvoiCommand(Application $app,Request $request){
        $id=$request->get('id');
        $this->commandeModel = new CommandeModel($app);
        $this->commandeModel->EnvoiCommand($id);
        return $app->redirect($app["url_generator"]->generate("Commande.show"));
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/show', 'App\Controller\CommandeController::show')->bind('Commande.show');
        $controllers->get('/ValideCommande', 'App\Controller\CommandeController::ValidCommand')->bind('Commande.ValidCommand');
        $controllers->post('/EnvoiCommande', 'App\Controller\CommandeController::EnvoiCommand')->bind('Commande.EnvoiCommand');
        $controllers->get('/Commande', 'App\Controller\CommandeController::CreateCommand')->bind('Commande.CreateCommand');

        return $controllers;
    }
}