<?php
// ============================================================
//  StageConnect – Configuration principale
// ============================================================

// ── BASE URL ──────────────────────────────────────────────────
define('BASE_URL',  'http://localhost/stageconnect');   // au lieu de stageconnect.local
define('STATIC_URL','http://localhost/stageconnect/public/assets');   // assets
define('APP_NAME',  'StageConnect');
define('APP_ENV',   'development');  // pas besoin de changer
// ── CHEMINS ───────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('VIEW_PATH',    APP_PATH  . '/Views');
define('UPLOAD_PATH',  ROOT_PATH . '/public/assets/uploads');
define('UPLOAD_URL',   BASE_URL  . '/assets/uploads');

// ── SESSION ───────────────────────────────────────────────────
define('SESSION_NAME',     'sc_session');
define('SESSION_LIFETIME', 3600);   // 1 heure

// ── PAGINATION ────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 10);

// ── SÉCURITÉ ──────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', '_csrf_token');

// ── ERREURS (désactiver en prod) ──────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
