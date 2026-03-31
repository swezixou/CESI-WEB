<?php
// ============================================================
//  StageConnect – Point d'entrée unique (Front Controller)
// ============================================================

declare(strict_types=1);

// ── Autoload PSR-4 simple (sans Composer) ──────────────────
spl_autoload_register(function (string $class): void {
    // 'App\Controllers\AuthController' → app/Controllers/AuthController.php
    $base = dirname(__DIR__) . '/app/';
    $rel  = str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    $file = $base . $rel;
    if (file_exists($file)) {
        require_once $file;
    }
});

// ── Configuration ───────────────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

// ── Session sécurisée ────────────────────────────────────────
use App\Core\Auth;
use App\Core\Csrf;
Auth::startSession();

// ── Routage ──────────────────────────────────────────────────
$router = require_once dirname(__DIR__) . '/app/routes.php';
$router->dispatch();
