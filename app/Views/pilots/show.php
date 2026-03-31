<?php use App\Core\{View}; ?>
<section class="page-header page-header-admin"><div class="page-header-inner">
  <a href="/admin/pilots" class="back-link">← Retour</a>
  <h1><?= View::e($pilot['firstname'].' '.$pilot['lastname']) ?></h1>
  <p>Pilote · <?= View::e($pilot['promotion']??'') ?></p>
</div></section>
<section class="page-body"><div class="main-content">
  <div class="dashboard-grid">
    <div class="dashboard-stats">
      <div class="stat-card"><div class="stat-card-value"><?= count($students) ?></div><div class="stat-card-label">Étudiants affectés</div></div>
      <div class="stat-card"><div class="stat-card-value"><?= $pilot['email'] ?></div><div class="stat-card-label">Email</div></div>
    </div>
    <div class="dashboard-section" style="grid-column:1/-1">
      <div class="section-header-sm"><h2>Étudiants de la promotion</h2></div>
      <div class="data-table-wrap"><table class="data-table">
        <thead><tr><th>Nom</th><th>Email</th><th>Statut</th></tr></thead>
        <tbody>
          <?php foreach ($students as $s): ?>
          <tr>
            <td><?= View::e($s['firstname'].' '.$s['lastname']) ?></td>
            <td><?= View::e($s['email']) ?></td>
            <td><span class="status-badge status-<?= $s['stage_status'] ?>"><?= $s['stage_status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div></section>
