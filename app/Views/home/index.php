<?php use App\Core\{Auth, View, Csrf}; ?>

<!-- ═══════ HERO ═══════ -->
<section class="hero">
  <div class="hero-bg"><div class="hero-blob b1"></div><div class="hero-blob b2"></div><div class="hero-blob b3"></div></div>
  <div class="hero-content">
    <div class="hero-badge"><span class="pulse-dot"></span> Plateforme officielle CESI</div>
    <h1>Trouve ton <em>stage idéal</em><br/>en quelques clics.</h1>
    <p>Des centaines d'offres sélectionnées par tes pilotes. Recherche, postule et gère tes candidatures depuis un seul espace.</p>
    <form action="/offers" method="GET" class="search-bar">
      <input type="text" name="search" placeholder="Développeur, Marketing, Data Science…" />
      <div class="search-divider"></div>
      <button type="submit">🔍 Rechercher</button>
    </form>
    <div class="hero-stats">
      <div class="hero-stat"><div class="hero-stat-value"><?= $stats['offers'] ?>+</div><div class="hero-stat-label">Offres actives</div></div>
      <div class="hero-stat"><div class="hero-stat-value"><?= $stats['companies'] ?></div><div class="hero-stat-label">Entreprises</div></div>
      <div class="hero-stat"><div class="hero-stat-value"><?= $stats['students'] ?></div><div class="hero-stat-label">Étudiants inscrits</div></div>
    </div>
  </div>
</section>

<!-- ═══════ RÔLES ═══════ -->
<section class="roles">
  <div class="section-label">Accès personnalisé</div>
  <h2 class="section-title">Un espace dédié à chaque profil.</h2>
  <div class="roles-grid">
    <a href="/login?role=student" class="role-card role-student">
      <div class="role-icon">🎓</div>
      <h3>Étudiant</h3>
      <p>Recherchez, postulez et gérez vos candidatures et wish-list.</p>
      <ul class="role-features">
        <li>Rechercher des offres par compétences</li>
        <li>Postuler avec CV & lettre de motivation</li>
        <li>Gérer une wish-list d'offres favorites</li>
        <li>Évaluer les entreprises visitées</li>
      </ul>
      <span class="role-cta">Accéder à mon espace →</span>
    </a>
    <a href="/login?role=pilot" class="role-card role-pilot">
      <div class="role-icon">🧭</div>
      <h3>Pilote de Promotion</h3>
      <p>Supervisez vos élèves et gérez les offres de stage.</p>
      <ul class="role-features">
        <li>Créer & modifier des offres de stage</li>
        <li>Suivre les candidatures de ses élèves</li>
        <li>Consulter CV et lettres de motivation</li>
        <li>Gérer les comptes étudiants</li>
      </ul>
      <span class="role-cta">Tableau de bord pilote →</span>
    </a>
    <a href="/login?role=admin" class="role-card role-admin">
      <div class="role-icon">⚙️</div>
      <h3>Administrateur</h3>
      <p>Gérez l'ensemble de la plateforme et des utilisateurs.</p>
      <ul class="role-features">
        <li>Gestion complète des utilisateurs</li>
        <li>Créer & supprimer des comptes pilotes</li>
        <li>Modérer entreprises & offres</li>
        <li>Accès aux statistiques globales</li>
      </ul>
      <span class="role-cta">Panneau d'administration →</span>
    </a>
  </div>
</section>

<!-- ═══════ DERNIÈRES OFFRES ═══════ -->
<section class="offers-section">
  <div class="section-header">
    <div>
      <div class="section-label">Dernières offres</div>
      <h2 class="section-title">Offres disponibles.</h2>
    </div>
    <a href="/offers" class="btn-outline-dark">Voir toutes les offres →</a>
  </div>
  <div class="offers-grid">
    <?php foreach ($latestOffers as $offer): ?>
    <a href="/offers/<?= $offer['id'] ?>" class="offer-card">
      <div class="offer-card-top">
        <div class="company-logo-sm"><?= strtoupper(substr($offer['company_name'], 0, 2)) ?></div>
      </div>
      <div class="offer-title"><?= View::e($offer['title']) ?></div>
      <div class="offer-company"><?= View::e($offer['company_name']) ?> · <?= View::e($offer['city']) ?></div>
      <div class="offer-tags">
        <span class="offer-tag tag-blue"><?= $offer['duration'] ?> sem.</span>
        <?php if ($offer['sector']): ?><span class="offer-tag tag-green"><?= View::e($offer['sector']) ?></span><?php endif; ?>
      </div>
      <div class="offer-footer">
        <span class="offer-salary"><?= $offer['salary'] ? number_format($offer['salary'], 0, ',', ' ') . ' €/mois' : 'Non précisé' ?></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ═══════ STATS BAND ═══════ -->
<div class="stats-band">
  <div class="stat-item"><div class="stat-value"><?= $stats['offers'] ?>+</div><div class="stat-label">Offres disponibles</div></div>
  <div class="stat-item"><div class="stat-value"><?= $stats['companies'] ?></div><div class="stat-label">Entreprises partenaires</div></div>
  <div class="stat-item"><div class="stat-value"><?= $stats['students'] ?></div><div class="stat-label">Étudiants inscrits</div></div>
</div>

<!-- ═══════ CTA ═══════ -->
<section class="cta-banner">
  <h2>Prêt(e) à décrocher votre stage ?</h2>
  <p>Rejoignez des centaines d'étudiants CESI qui ont trouvé leur stage via StageConnect.</p>
  <div class="cta-actions">
    <a href="/login?role=student" class="btn-dark">Se connecter a mon compte étudiant</a>
    <a href="/offers" class="btn-white-outline">Parcourir les offres</a>
  </div>
</section>
