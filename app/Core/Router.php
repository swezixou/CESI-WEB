<?php
namespace App\Core;

/**
 * Routeur URL – analyse l'URL et dispatch vers le bon contrôleur/action.
 * Supporte les paramètres dynamiques : /offers/show/{id}
 */
class Router
{
    private array $routes = [];

    // ── Enregistrement des routes ───────────────────────────────
    public function get(string $path, string $controller, string $action): void
    {
        $this->routes[] = ['GET', $path, $controller, $action];
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->routes[] = ['POST', $path, $controller, $action];
    }

    // ── Dispatch ───────────────────────────────────────────────
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = strtok($_SERVER['REQUEST_URI'], '?');
        $uri    = '/' . trim($uri, '/');

        foreach ($this->routes as [$routeMethod, $routePath, $controllerName, $action]) {
            if ($routeMethod !== $method) continue;

            $pattern = $this->buildPattern($routePath);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // retirer le match global
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Contrôleur $controllerName introuvable.");
                    return;
                }
                $ctrl = new $controllerClass();
                if (!method_exists($ctrl, $action)) {
                    $this->abort(500, "Action $action introuvable.");
                    return;
                }
                call_user_func_array([$ctrl, $action], $matches);
                return;
            }
        }

        $this->abort(404);
    }

    /** Convertit /offers/{id} en regex */
    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        $titles = [404 => 'Page introuvable', 500 => 'Erreur serveur', 403 => 'Accès refusé'];
        $title  = $titles[$code] ?? 'Erreur';
        include VIEW_PATH . '/layouts/error.php';
        exit;
    }
}
