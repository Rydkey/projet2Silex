<?php
include('config.php');

//On initialise le timeZone
ini_set('date.timezone', 'Europe/Paris');

//On ajoute l'autoloader (compatible winwin)
$loader = require_once join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'vendor', 'autoload.php']);
//dans l'autoloader nous ajoutons notre répertoire applicatif
$loader->addPsr4('App\\',__DIR__);

//Nous instancions un objet Silex\Application
$app = new Silex\Application();
// connexion à la base de données
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => hostname,
        'host' => hostname,
        'dbname' => database,
        'user' => username,
        'password' => password,
        'charset'   => 'utf8mb4',
    ),
));
//utilisation de twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, array(__DIR__, 'View'))
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// utilisation des sessoins
$app->register(new Silex\Provider\SessionServiceProvider());

//en dev, nous voulons voir les erreurs
$app['debug'] = true;

// rajoute la méthode asset dans twig
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        // implement whatever logic you need to determine the asset path
        return sprintf(BASE_URL.'%s', ltrim($asset, '/'));
    }));
    return $twig;
}));

// par défaut les méthodes DELETE PUT ne sont pas prises en compte
use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

// validator      => php composer.phar  require symfony/validator
$app->register(new Silex\Provider\ValidatorServiceProvider());

//***************************************
// Montage des controleurs sur le routeur
$app->mount("/", new App\Controller\IndexController());
$app->mount("/produit", new App\Controller\ProduitController($app));
$app->mount("/panier", new App\Controller\PanierController($app));
$app->mount("/connexion", new App\Controller\UserController($app));
$app->mount("/commande", new App\Controller\CommandeController($app));

//On lance l'application
$app->run();