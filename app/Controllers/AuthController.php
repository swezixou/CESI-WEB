<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\UserModel;

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::dashboardUrl());
        }
        $this->render('auth/login', [
            'pageTitle' => 'Connexion – ' . APP_NAME,
        ], 'auth');
    }

    public function loginProcess(): void
    {
        $this->validateCsrf();

        $email    = filter_var(trim($this->post('email', '')), FILTER_SANITIZE_EMAIL);
        $password = $this->post('password', '');

        // Validation basique
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            Flash::error('Veuillez renseigner un email valide et un mot de passe.');
            $this->redirect('/login');
        }

        // Chercher l'utilisateur
        $user = $this->userModel->findByEmail($email);

        // Vérifier le mot de passe
        if (!$user || !password_verify($password, $user['password'])) {
            Flash::error('Email ou mot de passe incorrect.');
            $this->redirect('/login');
        }

        // Compte actif ?
        if (!(bool)$user['is_active']) {
            Flash::error('Votre compte a été désactivé. Contactez un administrateur.');
            $this->redirect('/login');
        }

        // Connexion OK
        Auth::login($user);
        Flash::success('Bienvenue, ' . htmlspecialchars($user['firstname']) . ' !');
        $this->redirect(Auth::dashboardUrl());
    }

    public function logout(): void
    {
        Auth::logout();
        Flash::info('Vous êtes déconnecté(e).');
        $this->redirect('/login');
    }
}
