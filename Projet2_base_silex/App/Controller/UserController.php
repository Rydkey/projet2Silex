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
        if (isset($_POST['nom'])){
            $clients['nom'] = htmlspecialchars($_POST['nom']);
            if ((!preg_match("/[A-Za-z0-9]{2,}/", $clients['nom']))) $erreurs['nom'] = 'nom composé de 2 lettres minimum';
        }
        if (isset($_POST['adresse'])) {
            $clients['adresse'] = htmlspecialchars($_POST['adresse']);
            if ((!preg_match("/[A-Za-z0-9]{2,}/", $clients['adresse']))) $erreurs['adresse'] = 'adresse composé de 2 lettres minimum';
        }
        if (isset($_POST['ville'])) {
            $clients['ville'] = htmlspecialchars($_POST['ville']);
            if ((!preg_match("/^[A-Za-z ]{2,}/", $clients['ville']))) $erreurs['ville'] = 'ville composé de 2 lettres minimum';
        }
        if (isset($_POST['code_postal'])){
            $clients['code_postal'] = htmlspecialchars($_POST['code_postal']);
            if (!is_numeric($clients['code_postal'])) $erreurs['code_postal'] = 'veuillez saisir une valeur numérique';
        }
        if (isset($_POST['email'])) {
            $clients['email'] = htmlspecialchars($_POST['email']);
            if (!filter_var($clients['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = "format invalide";
            };
        }
        if (!empty($erreurs)) {
            return $app["twig"]->render('frontOff/User/userEdit.html.twig', ['clients' => $clients, 'erreurs' => $erreurs]);
        } else {
            $this->userModel=new UserModel($app);
            $this->userModel->updateUser($app['session']->get('idUser'),$clients);
            return $app->redirect($app["url_generator"]->generate("user.space"));
        }
    }
    
    public function editMDP(Application $app){
        $this->userModel=new UserModel($app);
        $clients = $this->userModel->getUser($app['session']->get('idUser'));
        return $app['twig']->render('frontOff/User/userEditMDP.html.twig',['clients'=>$clients]);
    }
    
    public function validFormEditMDP(Application $app)
    {
        $this->userModel = new UserModel($app);
        if (isset($_POST['oldMDP'])) {
            $clients['oldMDP'] = htmlspecialchars($_POST['oldMDP']);
            $data = $this->userModel->verif_login_mdp_Utilisateur($app['session']->get('login'), $clients['oldMDP']);
//            if ($data != NULL) $erreurs['oldMDP'] = 'Mauvais mot de passe';
        }
        if (isset($_POST['newMDP'])) {
            $clients['newMDP'] = htmlspecialchars($_POST['newMDP']);
            $data = $this->userModel->verif_login_mdp_Utilisateur($app['session']->get('login'), $clients['newMDP']);
            if ($data != NULL) $erreurs['newMDP'] = 'Entrez un mot de passe différent';
        }
        if (!empty($erreurs)) {
            return $app["twig"]->render('frontOff/User/userEditMDP.html.twig', ['clients' => $clients, 'erreurs' => $erreurs]);
        } else {
            $this->userModel = new UserModel($app);
            $this->userModel->updateMDP($app['session']->get('idUser'), $clients);
            return $app->redirect($app["url_generator"]->generate("user.space"));
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
		$controllers->get('/editMDP', 'App\Controller\UserController::editMDP')->bind('user.modifMDP');
		$controllers->post('/validEditMDP', 'App\Controller\UserController::validFormEditMDP')->bind('user.validFormEditMDP');
		return $controllers;
	}
}