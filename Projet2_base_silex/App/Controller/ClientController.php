<?php
/**
 * Created by PhpStorm.
 * User: rrisser
 * Date: 02/12/16
 * Time: 10:56
 */

namespace App\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use App\Model\ClientModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation

use Symfony\Component\Security;

class ClientController implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */

    private $ClientModel;

    public function showClient(Application $app){
        $this->ClientModel = new ClientModel($app);
        $clients=$this->ClientModel->getClient($app['session']->get('droit'));
        return $app["twig"]->render('backOff/Client/show.html.twig',['clients'=>$clients]);
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/show', 'App\Controller\ClientController::showClient')->bind('client.show');

        return $controllers;
    }
}