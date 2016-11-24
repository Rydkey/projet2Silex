<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;


class IndexController implements ControllerProviderInterface
{
    public function index(Application $app)
    {
        return $app["twig"]->render("layout.html.twig");
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/", 'App\Controller\IndexController::index')->bind('acceuil');
        return $index;
    }


}
