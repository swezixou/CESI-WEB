<?php
namespace App\Core;

/**
 * Classe Auth – Gère la session utilisateur et les rôles.
 * Les informations de session sont stockées côté serveur.
 * Conforme STx 11 (cookies sécurisés, aucune donnée sensible en clair).
 */
class Auth
{
    /** Démarre ou reprend la session sécurisée */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => true,   // true en HTTPS (production)
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    /** Connecte un utilisateur (stocke uniquement l'ID et le rôle en session) */
    public static function login(array $user): void
    {
        session_regenerate_id(true); // protection contre la fixation de session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
    }

    /** Déconnecte l'utilisateur */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    /** Vérifie si un utilisateur est connecté */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /** Retourne l'ID de l'utilisateur connecté */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /** Retourne le rôle de l'utilisateur connecté */
    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /** Retourne le nom affiché */
    public static function name(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    /** Vérifie si l'utilisateur a le rôle donné */
    public static function is(string $role): bool
    {
        return self::role() === $role;
    }

    /** Retourne le tableau user courant depuis la BDD */
    public static function user(): array|false
    {
        if (!self::check()) return false;
        $db = Database::getInstance();
        return $db->queryOne('SELECT * FROM users WHERE id = ?', [self::id()]);
    }

    /** URL du tableau de bord selon le rôle */
    public static function dashboardUrl(): string
    {
        return match(self::role()) {
            'admin'   => '/admin/dashboard',
            'pilot'   => '/pilot/dashboard',
            'student' => '/student/dashboard',
            default   => '/',
        };
    }
}
