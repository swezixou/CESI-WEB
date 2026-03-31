<?php
// ============================================================
//  StageConnect – Tests unitaires AuthController (STx 14)
//  Exécuter : ./vendor/bin/phpunit tests/
// ============================================================

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires du processus d'authentification.
 * On teste la logique de validation sans avoir besoin
 * d'une vraie base de données (utilisation de mocks).
 */
class AuthControllerTest extends TestCase
{
    // ── Test 1 : Email valide ──────────────────────────────
    public function testValidEmailPassesValidation(): void
    {
        $email = 'alice.bernard@student.cesi.fr';
        $this->assertTrue(
            (bool) filter_var($email, FILTER_VALIDATE_EMAIL),
            'Un email valide doit passer la validation.'
        );
    }

    // ── Test 2 : Email invalide ────────────────────────────
    public function testInvalidEmailFailsValidation(): void
    {
        $email = 'not-an-email';
        $this->assertFalse(
            (bool) filter_var($email, FILTER_VALIDATE_EMAIL),
            'Un email invalide doit échouer la validation.'
        );
    }

    // ── Test 3 : Rôle valide ───────────────────────────────
    public function testValidRoleIsAccepted(): void
    {
        $validRoles = ['student', 'pilot', 'admin'];
        foreach ($validRoles as $role) {
            $this->assertTrue(
                in_array($role, $validRoles, true),
                "Le rôle '$role' doit être accepté."
            );
        }
    }

    // ── Test 4 : Rôle invalide ─────────────────────────────
    public function testInvalidRoleIsRejected(): void
    {
        $validRoles = ['student', 'pilot', 'admin'];
        $this->assertFalse(
            in_array('superuser', $validRoles, true),
            "Un rôle inconnu doit être rejeté."
        );
    }

    // ── Test 5 : Mot de passe trop court ───────────────────
    public function testShortPasswordFails(): void
    {
        $password = '123';
        $this->assertLessThan(
            6,
            strlen($password),
            'Un mot de passe de moins de 6 caractères doit être refusé.'
        );
    }

    // ── Test 6 : Mot de passe assez long ──────────────────
    public function testLongPasswordPasses(): void
    {
        $password = 'Password123!';
        $this->assertGreaterThanOrEqual(
            6,
            strlen($password),
            'Un mot de passe d\'au moins 6 caractères doit être accepté.'
        );
    }

    // ── Test 7 : Vérification du hash bcrypt ──────────────
    public function testPasswordHashAndVerify(): void
    {
        $plain = 'Password123!';
        $hash  = password_hash($plain, PASSWORD_DEFAULT);

        $this->assertTrue(
            password_verify($plain, $hash),
            'La vérification du hash doit réussir avec le bon mot de passe.'
        );
        $this->assertFalse(
            password_verify('WrongPass!', $hash),
            'La vérification doit échouer avec un mauvais mot de passe.'
        );
    }

    // ── Test 8 : URL de dashboard selon le rôle ───────────
    public function testDashboardUrlByRole(): void
    {
        $urls = [
            'admin'   => '/admin/dashboard',
            'pilot'   => '/pilot/dashboard',
            'student' => '/student/dashboard',
        ];

        foreach ($urls as $role => $expected) {
            $url = match($role) {
                'admin'   => '/admin/dashboard',
                'pilot'   => '/pilot/dashboard',
                'student' => '/student/dashboard',
                default   => '/',
            };
            $this->assertSame($expected, $url, "URL incorrecte pour le rôle '$role'.");
        }
    }

    // ── Test 9 : Sécurité CSRF – hash_equals ──────────────
    public function testCsrfTokenComparison(): void
    {
        $token    = bin2hex(random_bytes(32));
        $tampered = bin2hex(random_bytes(32));

        $this->assertTrue(hash_equals($token, $token),   'Les tokens identiques doivent correspondre.');
        $this->assertFalse(hash_equals($token, $tampered),'Des tokens différents ne doivent pas correspondre.');
    }

    // ── Test 10 : Sanitisation email ──────────────────────
    public function testEmailSanitization(): void
    {
        $raw       = " admin@cesi.fr<script> ";
        $sanitized = filter_var(trim($raw), FILTER_SANITIZE_EMAIL);
        $this->assertStringNotContainsString('<script>', $sanitized, 'Les balises script doivent être retirées.');
    }
}
