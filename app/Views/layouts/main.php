<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="StageConnect CESI – Plateforme officielle de recherche de stages." />
  <meta name="keywords" content="stage, alternance, CESI, offre de stage, étudiant" />
  <title><?= \App\Core\View::e($pageTitle ?? APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/app.css" />
</head>
<body>

<nav class="navbar" id="navbar">
  <a href="/" class="nav-logo">
    <div class="nav-logo-badge">SC</div>
    <span>Stage<em>Connect</em></span>
  </a>

  <ul class="nav-links" id="navLinks">
    <li><a href="/offers"    <?= str_starts_with($_SERVER['REQUEST_URI'],'/offers')    ? 'class="active"':'' ?>>Offres de stage</a></li>
    <li><a href="/companies" <?= str_starts_with($_SERVER['REQUEST_URI'],'/companies') ? 'class="active"':'' ?>>Entreprises</a></li>
    <?php if (\App\Core\Auth::is('student')): ?>
      <li><a href="/student/wishlist">Ma wish-list</a></li>
      <li><a href="/student/applications">Mes candidatures</a></li>
    <?php elseif (\App\Core\Auth::is('pilot')): ?>
      <li><a href="/pilot/dashboard">Tableau de bord</a></li>
      <li><a href="/pilot/applications">Candidatures</a></li>
      <li><a href="/offers/create">Créer une offre</a></li>
    <?php elseif (\App\Core\Auth::is('admin')): ?>
      <li><a href="/admin/dashboard">Administration</a></li>
      <li><a href="/admin/users">Utilisateurs</a></li>
      <li><a href="/admin/pilots">Pilotes</a></li>
    <?php endif; ?>
  </ul>

  <div class="nav-actions" id="navActions">
    <?php if (\App\Core\Auth::check()): ?>
      <div class="nav-user">
        <span class="nav-user-badge <?= \App\Core\Auth::role() ?>"><?= \App\Core\Auth::role() === 'admin' ? '⚙️' : (\App\Core\Auth::role() === 'pilot' ? '🧭' : '🎓') ?></span>
        <span class="nav-user-name"><?= \App\Core\View::e(\App\Core\Auth::name()) ?></span>
        <div class="nav-dropdown">
          <?php if (\App\Core\Auth::is('student')): ?>
            <a href="/student/dashboard">Mon espace</a>
          <?php elseif (\App\Core\Auth::is('pilot')): ?>
            <a href="/pilot/dashboard">Tableau de bord</a>
          <?php else: ?>
            <a href="/admin/dashboard">Administration</a>
          <?php endif; ?>
          <a href="/logout" class="logout-link">Se déconnecter</a>
        </div>
      </div>
    <?php else: ?>
      <a href="/login" class="btn-primary">Connexion</a>
    <?php endif; ?>
  </div>

  <div class="burger" id="burger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="/offers" onclick="toggleMenu()">💼 Offres de stage</a>
  <a href="/companies" onclick="toggleMenu()">🏢 Entreprises</a>
  <?php if (\App\Core\Auth::is('student')): ?>
    <a href="/student/wishlist" onclick="toggleMenu()">❤️ Ma wish-list</a>
    <a href="/student/applications" onclick="toggleMenu()">📁 Mes candidatures</a>
    <a href="/student/dashboard" onclick="toggleMenu()">🏠 Mon espace</a>
  <?php elseif (\App\Core\Auth::is('pilot')): ?>
    <a href="/pilot/dashboard" onclick="toggleMenu()">📊 Tableau de bord</a>
    <a href="/pilot/applications" onclick="toggleMenu()">📋 Candidatures</a>
    <a href="/offers/create" onclick="toggleMenu()">➕ Créer une offre</a>
  <?php elseif (\App\Core\Auth::is('admin')): ?>
    <a href="/admin/dashboard" onclick="toggleMenu()">⚙️ Administration</a>
    <a href="/admin/users" onclick="toggleMenu()">👥 Utilisateurs</a>
    <a href="/admin/pilots" onclick="toggleMenu()">🧭 Pilotes</a>
  <?php endif; ?>
  <div class="mobile-actions">
    <?php if (\App\Core\Auth::check()): ?>
      <a href="/logout" style="color:var(--accent-3)">↪ Se déconnecter</a>
    <?php else: ?>
      <a href="/login" class="btn-primary" style="text-align:center">Connexion</a>
    <?php endif; ?>
  </div>
</div>

<?php $flashes = \App\Core\Flash::get(); ?>
<?php if ($flashes): ?>
  <div class="flash-container">
    <?php foreach ($flashes as $flash): ?>
      <div class="flash flash-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<main id="main">
  <?= $content ?>
</main>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="nav-logo" style="color:#fff">
        <div class="nav-logo-badge">SC</div>
        <span>Stage<em>Connect</em></span>
      </div>
      <p>Plateforme officielle de stages CESI – Projet Web4All.</p>
    </div>
    <div class="footer-col">
      <h5>Navigation</h5>
      <ul>
        <li><a href="/offers">Offres de stage</a></li>
        <li><a href="/companies">Entreprises</a></li>
        <?php if (\App\Core\Auth::is('student')): ?>
          <li><a href="/student/wishlist">Ma wish-list</a></li>
          <li><a href="/student/applications">Mes candidatures</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Mon espace</h5>
      <ul>
        <?php if (\App\Core\Auth::check()): ?>
          <li><a href="<?= \App\Core\Auth::dashboardUrl() ?>">Mon tableau de bord</a></li>
          <li><a href="/logout">Se déconnecter</a></li>
        <?php else: ?>
          <li><a href="/login">Se connecter</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Légal</h5>
      <ul>
        <li><a href="/mentions-legales">Mentions légales</a></li>
        <li><a href="/politique-confidentialite">Confidentialité</a></li>
        <li><a href="/cookies">Cookies</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> StageConnect – Web4All CESI</p>
    <div class="footer-legal">
      <a href="/mentions-legales">Mentions légales</a>
      <a href="/politique-confidentialite">Confidentialité</a>
      <a href="/cookies">Cookies</a>
    </div>
  </div>
</footer>

<script src="/assets/js/app.js"></script>
</body>
</html>
