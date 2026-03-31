<?php use App\Core\View; ?>
<section class="page-header"><div class="page-header-inner">
  <a href="/students" class="back-link">← Retour</a>
  <h1><?= View::e($student['firstname'].' '.$student['lastname']) ?></h1>
  <p><?= View::e($student['email']) ?></p>
</div></section>
<section class="page-body"><div class="main-content">
  <div class="dashboard-grid">
    <div class="dashboard-stats">
      <div class="stat-card"><div class="stat-card-value"><span class="status-badge status-<?= $student['stage_status'] ?>"><?= $student['stage_status'] ?></span></div><div class="stat-card-label">Statut stage</div></div>
      <div class="stat-card"><div class="stat-card-value"><?= count($apps) ?></div><div class="stat-card-label">Candidatures</div></div>
      <div class="stat-card"><div class="stat-card-value"><?= View::e($student['promotion']??'—') ?></div><div class="stat-card-label">Promotion</div></div>
    </div>
    <div class="dashboard-section" style="grid-column:1/-1">
      <div class="section-header-sm"><h2>Candidatures de l'étudiant</h2></div>
      <?php if (empty($apps)): ?>
        <div class="empty-state-sm">Aucune candidature.</div>
      <?php else: ?>
        <div class="data-table-wrap"><table class="data-table">
          <thead><tr><th>Offre</th><th>Entreprise</th><th>Statut</th><th>Date</th><th>CV</th></tr></thead>
          <tbody>
            <?php foreach ($apps as $app): ?>
            <tr>
              <td><a href="/offers/<?= $app['offer_id'] ?>"><?= View::e($app['title']) ?></a></td>
              <td><?= View::e($app['company_name']) ?></td>
              <td><span class="status-badge status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
              <td><?= date('d/m/Y',strtotime($app['applied_at'])) ?></td>
              <td><?= $app['cv_path']?'<a href="/assets/uploads/'.$app['cv_path'].'" target="_blank">📄 CV</a>':'—' ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div></section>
