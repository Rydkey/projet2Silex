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

    public function validFormEdit(Application $app, Request $req){
        if(isset($_POST['login']) && isset($_POST['email']))
            $donnees=[
                'nom'=>htmlspecialchars($_POST['nom'])
        ];
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