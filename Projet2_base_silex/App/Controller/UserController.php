<?php
namespace App\Controller;

use App\Model\UserModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController implements ControllerProviderInterface {

	private $userModel;

	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('v_session_connexion.html.twig');
	}

	public function validFormConnexionUser(Application $app)
	{

		$app['session']->clear();
		$donnees['login']=$app['request']->request->get('login');
		$donnees['password']=$app['request']->request->get('password');

		$this->userModel = new UserModel($app);
		$data=$this->userModel->verif_login_mdp_Utilisateur($donnees['login'],$donnees['password']);

		if($data != NULL)
		{
			$app['session']->set('droit', $data['droit']);  //dans twig {{ app.session.get('droit') }}
			$app['session']->set('login', $data['login']);
			$app['session']->set('logged', 1);
			$app['session']->set('idUser',$data['id']);
			return $app->redirect($app["url_generator"]->generate("accueil"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('v_session_connexion.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("accueil"));
	}
	
	public function showUser(Application $app){
		$this->userModel=new UserModel($app);
		$clients=$this->userModel->getUser($app['session']->get('idUser'));
		return $app['twig']->render('frontOff/User/userSpace.html.twig',['clients'=> $clients ]);
	}
	
	public function editUser(Application $app){
		$this->userModel=new UserModel($app);
		$clients = $this->userModel->getUser($app['session']->get('idUser'));
		return $app['twig']->render('frontOff/User/userEdit.html.twig',['clients'=>$clients]);
	}

    public function validFormEdit(Application $app){
        if (isset($_POST['nom']) && isset($_POST['adresse']) && isset($_POST['ville']) && isset($_POST['code_postal']) && isset($_POST['email'])) {
            $clients = [
                'nom' => htmlspecialchars($_POST['nom']),
                'adresse' => htmlspecialchars($_POST['adresse']),
                'ville' => htmlspecialchars($_POST['ville']),
                'code_postal' => htmlspecialchars($_POST['code_postal']),
                'email' => htmlspecialchars($_POST['email']),
            ];
            if ((!preg_match("/^[A-Za-z ]{2,}/", $clients['nom']))) $erreurs['nom'] = 'nom composé de 2 lettres minimum';
            if ((!preg_match("/^[A-Za-z ]{2,}/", $clients['adresse']))) $erreurs['adresse'] = 'adresse composé de 2 lettres minimum';
            if ((!preg_match("/^[A-Za-z ]{2,}/", $clients['ville']))) $erreurs['ville'] = 'ville composé de 2 lettres minimum';
            if (!is_numeric($clients['code_postal'])) $erreurs['code_postal'] = 'veuillez saisir une valeur';
            if (!filter_var($clients['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = "Invalid email format";
            };
            if (count($erreurs) > 0) {
                return $app["twig"]->render('frontOff/User/userEdit.html.twig', ['clients' => $clients, 'erreurs' => $erreurs]);
            } else {
            return $this->showUser($app);
            }
        }else{
            return 'bite';
        }
    }

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		$controllers->match('/', 'App\Controller\UserController::index')->bind('user.index');
		$controllers->get('/login', 'App\Controller\UserController::connexionUser')->bind('user.login');
		$controllers->post('/login', 'App\Controller\UserController::validFormConnexionUser')->bind('user.validFormlogin');
		$controllers->get('/logout', 'App\Controller\UserController::deconnexionSession')->bind('user.logout');
		$controllers->get('/personalSpace', 'App\Controller\UserController::showUser')->bind('user.space');
		$controllers->get('/editProfile', 'App\Controller\UserController::editUser')->bind('user.modif');
		$controllers->put('/edit', 'App\Controller\UserController::validFormEdit')->bind('user.validFormEdit');
		return $controllers;
	}
}