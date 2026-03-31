<?php
namespace App\Core;

/**
 * Protection CSRF – token dans la session (STx 11).
 */
class Csrf
{
    /** Génère (ou réutilise) un token CSRF */
    public static function token(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /** Retourne le champ hidden HTML à insérer dans chaque formulaire */
    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            CSRF_TOKEN_NAME,
            self::token()
        );
    }

    /** Valide le token soumis – interrompt si invalide */
    public static function validate(): void
    {
        $submitted = $_POST[CSRF_TOKEN_NAME] ?? '';
        $stored    = $_SESSION[CSRF_TOKEN_NAME] ?? '';

        if (!hash_equals($stored, $submitted)) {
            http_response_code(403);
            die('Token CSRF invalide. Veuillez recharger la page.');
        }
        // Renouvelle le token après validation
        unset($_SESSION[CSRF_TOKEN_NAME]);
    }
}
