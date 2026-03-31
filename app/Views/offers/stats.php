<?php use App\Core\View; ?>
<section class="page-header"><div class="page-header-inner"><h1>Statistiques des offres</h1><p>SFx 11 – Indicateurs clés</p></div></section>
<section class="page-body"><div class="main-content">
  <div class="dashboard-grid">
    <div class="dashboard-stats">
      <div class="stat-card"><div class="stat-card-value"><?= $stats['total_active'] ?></div><div class="stat-card-label">Offres actives en base</div></div>
      <div class="stat-card"><div class="stat-card-value"><?= round($stats['avg_applications'],1) ?></div><div class="stat-card-label">Moyenne candidatures / offre</div></div>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Répartition par durée</h2></div>
      <?php foreach ($stats['by_duration'] as $row): ?>
      <div class="bar-row">
        <span class="bar-label"><?= $row['duration'] ?> sem.</span>
        <div class="bar-track"><div class="bar-fill" style="width:<?= min(100,$row['total']*15) ?>%"><?= $row['total'] ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="dashboard-section">
      <div class="section-header-sm"><h2>Top offres les plus wish-listées</h2></div>
      <div class="list-cards">
        <?php foreach ($stats['top_wishlisted'] as $i => $o): ?>
        <div class="list-card">
          <div class="list-card-body"><div class="list-card-title"><?= ($i+1) ?>. <?= View::e($o['title']) ?></div><div class="list-card-sub"><?= View::e($o['company_name']) ?></div></div>
          <span class="badge-count">❤️ <?= $o['wl_count'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div></section>
