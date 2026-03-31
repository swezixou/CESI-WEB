<?php use App\Core\{View}; ?>
<section class="page-header">
  <div class="page-header-inner">
    <div class="dashboard-welcome">
      <div class="avatar-lg">🧭</div>
      <div>
        <h1>Bonjour, <?= View::e($user['firstname']) ?> !</h1>
        <p>Pilote · <?= View::e($pilot['promotion'] ?? '') ?></p>
      </div>
    </div>
  </div>
</section>
<section class="page-body">
  <div class="dashboard-grid">
    <div class="dashboard-stats">
      <div class="stat-card accent-pilot"><div class="stat-card-value"><?= count($students) ?></div><div class="stat-card-label">Étudiants</div><a href="/students" class="stat-card-link">Voir →</a></div>
      <div class="stat-card accent-pilot"><div class="stat-card-value"><?= count($apps) ?></div><div class="stat-card-label">Candidatures</div><a href="/pilot/applications" class="stat-card-link">Voir →</a></div>
      <div class="stat-card accent-pilot"><div class="stat-card-value"><?= count(array_filter($apps, fn($a) => $a['status'] === 'pending')) ?></div><div class="stat-card-label">En attente</div></div>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Mes étudiants</h2><a href="/students" class="link-more">Gérer →</a></div>
      <?php if (empty($students)): ?>
        <div class="empty-state-sm">Aucun étudiant. <a href="/students/create">Créer un compte →</a></div>
      <?php else: ?>
        <div class="list-cards">
          <?php foreach (array_slice($students,0,6) as $s): ?>
          <div class="list-card">
            <div class="list-card-body"><div class="list-card-title"><?= View::e($s['firstname'].' '.$s['lastname']) ?></div><div class="list-card-sub"><?= View::e($s['email']) ?></div></div>
            <span class="status-badge status-<?= $s['stage_status'] ?>"><?= match($s['stage_status']){'searching'=>'🔍','applied'=>'📬','found'=>'✅',default=>$s['stage_status']} ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Dernières candidatures</h2><a href="/pilot/applications" class="link-more">Voir tout</a></div>
      <?php if (empty($apps)): ?>
        <div class="empty-state-sm">Aucune candidature.</div>
      <?php else: ?>
        <div class="list-cards">
          <?php foreach (array_slice($apps,0,5) as $app): ?>
          <div class="list-card">
            <div class="list-card-body"><div class="list-card-title"><?= View::e($app['firstname'].' '.$app['lastname']) ?></div><div class="list-card-sub">→ <?= View::e($app['title']) ?></div></div>
            <span class="status-badge status-<?= $app['status'] ?>"><?= match($app['status']){'pending'=>'⏳','reviewed'=>'👀','accepted'=>'✅','rejected'=>'❌',default=>$app['status']} ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="dashboard-section dashboard-actions">
      <h2>Actions rapides</h2>
      <a href="/offers/create" class="action-card accent-pilot"><span class="action-icon">➕</span><span>Créer une offre</span></a>
      <a href="/students/create" class="action-card accent-pilot"><span class="action-icon">👤</span><span>Ajouter étudiant</span></a>
      <a href="/companies/create" class="action-card accent-pilot"><span class="action-icon">🏢</span><span>Ajouter entreprise</span></a>
      <a href="/offers/stats" class="action-card accent-pilot"><span class="action-icon">📈</span><span>Statistiques</span></a>
    </div>
  </div>
</section>
