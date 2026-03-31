<?php use App\Core\View; ?>
<section class="page-header"><div class="page-header-inner"><h1>Mes candidatures</h1><p><?= count($apps) ?> candidature(s)</p></div></section>
<section class="page-body"><div class="main-content">
  <?php if (empty($apps)): ?>
    <div class="empty-state"><div class="empty-icon">📁</div><h3>Aucune candidature</h3><a href="/offers" class="btn-primary">Voir les offres →</a></div>
  <?php else: ?>
    <div class="data-table-wrap"><table class="data-table">
      <thead><tr><th>Offre</th><th>Entreprise</th><th>Salaire</th><th>Statut</th><th>Date</th><th>CV</th></tr></thead>
      <tbody>
        <?php foreach ($apps as $app): ?>
        <tr>
          <td><a href="/offers/<?= $app['offer_id'] ?>"><?= View::e($app['title']) ?></a></td>
          <td><?= View::e($app['company_name']) ?></td>
          <td><?= $app['salary']?number_format($app['salary'],0,',',' ').' €':'—' ?></td>
          <td><span class="status-badge status-<?= $app['status'] ?>"><?= match($app['status']){'pending'=>'⏳ En attente','reviewed'=>'👀 En cours','accepted'=>'✅ Acceptée','rejected'=>'❌ Refusée',default=>$app['status']} ?></span></td>
          <td><?= date('d/m/Y',strtotime($app['applied_at'])) ?></td>
          <td><?= $app['cv_path']?'<a href="/assets/uploads/'.View::e($app['cv_path']).'" target="_blank" class="btn-xs">📄 CV</a>':'—' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div></section>
