<?php
namespace App\Core;

/**
 * Contrôleur de base – toutes les classes héritent de celui-ci.
 */
abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Affiche un template avec son layout.
     */
    protected function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $this->view->render($template, $data, $layout);
    }

    /**
     * Redirige vers une URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    /**
     * Retourne une réponse JSON (pour les appels AJAX).
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Vérifie que l'utilisateur est connecté, sinon redirige.
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
    }

    /**
     * Vérifie le rôle de l'utilisateur connecté.
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        if (!in_array(Auth::role(), $roles, true)) {
            http_response_code(403);
            $code    = 403;
            $title   = 'Accès refusé';
            $message = 'Vous n\'avez pas les permissions nécessaires.';
            include VIEW_PATH . '/layouts/error.php';
            exit;
        }
    }

    /**
     * Lit une valeur POST et la nettoie.
     */
    protected function post(string $key, mixed $default = ''): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Lit une valeur GET et la nettoie.
     */
    protected function get(string $key, mixed $default = ''): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /**
     * Valide le token CSRF.
     */
    protected function validateCsrf(): void
    {
        Csrf::validate();
    }
}
