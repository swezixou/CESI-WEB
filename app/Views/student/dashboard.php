<?php use App\Core\{Auth, View}; ?>

<section class="page-header">
  <div class="page-header-inner">
    <div class="dashboard-welcome">
      <div class="avatar-lg">🎓</div>
      <div>
        <h1>Bonjour, <?= View::e($user['firstname']) ?> !</h1>
        <p>Espace Étudiant · <?= View::e($student['stage_status'] === 'found' ? '✅ Stage trouvé' : ($student['stage_status'] === 'applied' ? '📬 Candidatures en cours' : '🔍 En recherche')) ?></p>
      </div>
    </div>
  </div>
</section>

<section class="page-body">
  <div class="dashboard-grid">

    <div class="dashboard-stats">
      <div class="stat-card">
        <div class="stat-card-value"><?= count($apps) ?></div>
        <div class="stat-card-label">Candidatures</div>
        <a href="/student/applications" class="stat-card-link">Voir →</a>
      </div>
      <div class="stat-card">
        <div class="stat-card-value"><?= count($wishlist) ?></div>
        <div class="stat-card-label">Wish-list</div>
        <a href="/student/wishlist" class="stat-card-link">Voir →</a>
      </div>
      <div class="stat-card">
        <div class="stat-card-value"><?= count(array_filter($apps, fn($a) => $a['status'] === 'pending')) ?></div>
        <div class="stat-card-label">En attente</div>
      </div>
    </div>

    <div class="dashboard-section">
      <div class="section-header-sm">
        <h2>Mes candidatures récentes</h2>
        <a href="/student/applications" class="link-more">Voir tout</a>
      </div>
      <?php if (empty($apps)): ?>
        <div class="empty-state-sm">Aucune candidature encore. <a href="/offers">Parcourir les offres →</a></div>
      <?php else: ?>
        <div class="list-cards">
          <?php foreach (array_slice($apps, 0, 4) as $app): ?>
          <div class="list-card">
            <div class="list-card-body">
              <div class="list-card-title"><?= View::e($app['title']) ?></div>
              <div class="list-card-sub"><?= View::e($app['company_name']) ?></div>
            </div>
            <span class="status-badge status-<?= $app['status'] ?>">
              <?= match($app['status']) {
                'pending'  => '⏳ En attente',
                'reviewed' => '👀 En cours',
                'accepted' => '✅ Acceptée',
                'rejected' => '❌ Refusée',
                default    => $app['status']
              } ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="dashboard-section">
      <div class="section-header-sm">
        <h2>Ma wish-list</h2>
        <a href="/student/wishlist" class="link-more">Voir tout</a>
      </div>
      <?php if (empty($wishlist)): ?>
        <div class="empty-state-sm">Aucune offre sauvegardée. <a href="/offers">Découvrir les offres →</a></div>
      <?php else: ?>
        <div class="list-cards">
          <?php foreach (array_slice($wishlist, 0, 4) as $offer): ?>
          <div class="list-card">
            <div class="list-card-body">
              <div class="list-card-title"><?= View::e($offer['title']) ?></div>
              <div class="list-card-sub"><?= View::e($offer['company_name']) ?> · <?= View::e($offer['city'] ?? '') ?></div>
            </div>
            <a href="/offers/<?= $offer['id'] ?>" class="btn-xs">Voir →</a>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="dashboard-section dashboard-actions">
      <h2>Actions rapides</h2>
      <a href="/offers" class="action-card"><span class="action-icon">🔍</span><span>Rechercher des offres</span></a>
      <a href="/student/wishlist" class="action-card"><span class="action-icon">❤️</span><span>Ma wish-list</span></a>
      <a href="/student/applications" class="action-card"><span class="action-icon">📁</span><span>Mes candidatures</span></a>
      <a href="/companies" class="action-card"><span class="action-icon">🏢</span><span>Explorer les entreprises</span></a>
    </div>

  </div>
</section>
