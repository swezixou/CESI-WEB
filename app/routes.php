<?php
// ============================================================
//  StageConnect – Table de routage
//  IMPORTANT : Les routes statiques (create, stats…) doivent
//  être déclarées AVANT les routes dynamiques ({id}).
// ============================================================

use App\Core\Router;

$router = new Router();

// ── PUBLIC ────────────────────────────────────────────────────
$router->get('/',               'HomeController',    'index');

// ── AUTH ──────────────────────────────────────────────────────
$router->get('/login',          'AuthController',    'loginForm');
$router->post('/login',         'AuthController',    'loginProcess');
$router->get('/logout',         'AuthController',    'logout');

// ── OFFRES ────────────────────────────────────────────────────
// ⚠️ Routes statiques AVANT /offers/{id}
$router->get('/offers',                 'OfferController',  'index');
$router->get('/offers/stats',           'OfferController',  'stats');
$router->get('/offers/create',          'OfferController',  'createForm');
$router->post('/offers/create',         'OfferController',  'create');
$router->get('/offers/{id}',            'OfferController',  'show');
$router->get('/offers/{id}/edit',       'OfferController',  'editForm');
$router->post('/offers/{id}/edit',      'OfferController',  'update');
$router->post('/offers/{id}/delete',    'OfferController',  'delete');
$router->post('/offers/{id}/apply',     'ApplicationController', 'apply');

// ── ENTREPRISES ───────────────────────────────────────────────
// ⚠️ Routes statiques AVANT /companies/{id}
$router->get('/companies',               'CompanyController', 'index');
$router->get('/companies/create',        'CompanyController', 'createForm');
$router->post('/companies/create',       'CompanyController', 'create');
$router->get('/companies/{id}',          'CompanyController', 'show');
$router->get('/companies/{id}/edit',     'CompanyController', 'editForm');
$router->post('/companies/{id}/edit',    'CompanyController', 'update');
$router->post('/companies/{id}/review',  'CompanyController', 'review');
$router->post('/companies/{id}/delete',  'CompanyController', 'delete');

// ── ÉTUDIANTS ─────────────────────────────────────────────────
// ⚠️ Routes statiques AVANT /students/{id}
$router->get('/students',               'StudentController', 'index');
$router->get('/students/create',        'StudentController', 'createForm');
$router->post('/students/create',       'StudentController', 'create');
$router->get('/students/{id}',          'StudentController', 'show');
$router->get('/students/{id}/edit',     'StudentController', 'editForm');
$router->post('/students/{id}/edit',    'StudentController', 'update');
$router->post('/students/{id}/delete',  'StudentController', 'delete');

// ── DASHBOARD ÉTUDIANT ────────────────────────────────────────
$router->get('/student/dashboard',      'StudentController',     'dashboard');
$router->get('/student/applications',   'ApplicationController', 'myApplications');
$router->get('/student/wishlist',       'WishlistController',    'index');

// ── WISH-LIST ─────────────────────────────────────────────────
$router->post('/wishlist/{id}/add',     'WishlistController',    'add');
$router->post('/wishlist/{id}/remove',  'WishlistController',    'remove');

// ── DASHBOARD PILOTE ──────────────────────────────────────────
$router->get('/pilot/dashboard',        'PilotController',       'dashboard');
$router->get('/pilot/applications',     'ApplicationController', 'pilotApplications');

// ── ADMIN ─────────────────────────────────────────────────────
$router->get('/admin/dashboard',             'AdminController', 'dashboard');
$router->get('/admin/users',                 'AdminController', 'users');
$router->post('/admin/users/{id}/toggle',    'AdminController', 'toggleUser');
$router->post('/admin/users/{id}/delete',    'AdminController', 'deleteUser');

// ⚠️ Routes statiques admin/pilots AVANT /admin/pilots/{id}
$router->get('/admin/pilots',                'PilotController', 'index');
$router->get('/admin/pilots/create',         'PilotController', 'createForm');
$router->post('/admin/pilots/create',        'PilotController', 'create');
$router->get('/admin/pilots/{id}',           'PilotController', 'show');
$router->get('/admin/pilots/{id}/edit',      'PilotController', 'editForm');
$router->post('/admin/pilots/{id}/edit',     'PilotController', 'update');
$router->post('/admin/pilots/{id}/delete',   'PilotController', 'delete');

// ── LÉGAL (SFx28) ─────────────────────────────────────────────
$router->get('/mentions-legales',            'LegalController', 'mentions');
$router->get('/politique-confidentialite',   'LegalController', 'confidentialite');
$router->get('/cookies',                     'LegalController', 'cookies');

return $router;
