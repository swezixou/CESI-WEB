<?php use App\Core\View; ?>
<section class="page-header page-header-admin">
  <div class="page-header-inner">
    <div class="dashboard-welcome"><div class="avatar-lg">⚙️</div><div><h1>Administration</h1><p>Vue globale de la plateforme</p></div></div>
  </div>
</section>
<section class="page-body">
  <div class="dashboard-grid">
    <div class="dashboard-stats dashboard-stats-6">
      <div class="stat-card accent-admin"><div class="stat-card-value"><?= $stats['offers'] ?></div><div class="stat-card-label">Offres actives</div><a href="/offers" class="stat-card-link">Gérer →</a></div>
      <div class="stat-card accent-admin"><div class="stat-card-value"><?= $stats['companies'] ?></div><div class="stat-card-label">Entreprises</div><a href="/companies" class="stat-card-link">Gérer →</a></div>
      <div class="stat-card accent-admin"><div class="stat-card-value"><?= $stats['students'] ?></div><div class="stat-card-label">Étudiants</div><a href="/students" class="stat-card-link">Gérer →</a></div>
      <div class="stat-card accent-admin"><div class="stat-card-value"><?= $stats['pilots'] ?></div><div class="stat-card-label">Pilotes</div><a href="/admin/pilots" class="stat-card-link">Gérer →</a></div>
      <div class="stat-card accent-admin"><div class="stat-card-value"><?= $stats['applications'] ?></div><div class="stat-card-label">Candidatures</div></div>
      <div class="stat-card accent-admin accent-warn"><div class="stat-card-value"><?= $stats['pending_apps'] ?></div><div class="stat-card-label">⏳ En attente</div></div>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Candidatures récentes</h2></div>
      <div class="list-cards">
        <?php foreach ($recentApps as $app): ?>
        <div class="list-card">
          <div class="list-card-body">
            <div class="list-card-title"><?= View::e($app['firstname'].' '.$app['lastname']) ?></div>
            <div class="list-card-sub">→ <?= View::e($app['title']) ?> · <?= View::e($app['company_name']) ?></div>
          </div>
          <span class="status-badge status-<?= $app['status'] ?>"><?= match($app['status']){'pending'=>'⏳','reviewed'=>'👀','accepted'=>'✅','rejected'=>'❌',default=>$app['status']} ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Top wish-listées</h2><a href="/offers/stats" class="link-more">Stats →</a></div>
      <div class="list-cards">
        <?php foreach ($topOffers as $i => $o): ?>
        <div class="list-card">
          <div class="list-card-body"><div class="list-card-title"><?= ($i+1) ?>. <?= View::e($o['title']) ?></div><div class="list-card-sub"><?= View::e($o['company_name']) ?></div></div>
          <span class="badge-count">❤️ <?= $o['wl_count'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Offres par durée</h2></div>
      <?php foreach ($offersByDuration as $row): ?>
      <div class="bar-row">
        <span class="bar-label"><?= $row['duration'] ?> sem.</span>
        <div class="bar-track"><div class="bar-fill" style="width:<?= min(100,$row['total']*15) ?>%"><?= $row['total'] ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="dashboard-section dashboard-actions">
      <h2>Administration rapide</h2>
      <a href="/admin/users" class="action-card accent-admin"><span class="action-icon">👥</span><span>Utilisateurs</span></a>
      <a href="/admin/pilots" class="action-card accent-admin"><span class="action-icon">🧭</span><span>Pilotes</span></a>
      <a href="/admin/pilots/create" class="action-card accent-admin"><span class="action-icon">➕</span><span>Créer pilote</span></a>
      <a href="/students/create" class="action-card accent-admin"><span class="action-icon">🎓</span><span>Créer étudiant</span></a>
      <a href="/companies/create" class="action-card accent-admin"><span class="action-icon">🏢</span><span>Créer entreprise</span></a>
      <a href="/offers/stats" class="action-card accent-admin"><span class="action-icon">📊</span><span>Statistiques</span></a>
    </div>
  </div>
</section>
