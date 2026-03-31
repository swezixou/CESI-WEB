<?php use App\Core\View; ?>
<section class="page-header"><div class="page-header-inner"><h1>Candidatures de ma promotion</h1><p><?= count($apps) ?> candidature(s)</p></div></section>
<section class="page-body"><div class="main-content">
  <?php if (empty($apps)): ?>
    <div class="empty-state"><div class="empty-icon">📊</div><h3>Aucune candidature pour le moment.</h3></div>
  <?php else: ?>
    <div class="data-table-wrap"><table class="data-table">
      <thead><tr><th>Étudiant</th><th>Offre</th><th>Entreprise</th><th>Statut</th><th>Date</th><th>Documents</th></tr></thead>
      <tbody>
        <?php foreach ($apps as $app): ?>
        <tr>
          <td><?= View::e($app['firstname'].' '.$app['lastname']) ?></td>
          <td><a href="/offers/<?= $app['offer_id'] ?>"><?= View::e($app['title']) ?></a></td>
          <td><?= View::e($app['company_name']) ?></td>
          <td><span class="status-badge status-<?= $app['status'] ?>"><?= match($app['status']){'pending'=>'⏳ Attente','reviewed'=>'👀 En cours','accepted'=>'✅ Acceptée','rejected'=>'❌ Refusée',default=>$app['status']} ?></span></td>
          <td><?= date('d/m/Y',strtotime($app['applied_at'])) ?></td>
          <td>
            <?= $app['cv_path']?'<a href="/assets/uploads/'.View::e($app['cv_path']).'" target="_blank" class="btn-xs">📄 CV</a>':'—' ?>
            <?php if($app['cover_letter']): ?>
              <button class="btn-xs" onclick="document.getElementById('lm<?= $app['id'] ?>').style.display='flex'">📝 LM</button>
              <div id="lm<?= $app['id'] ?>" class="modal-overlay" style="display:none">
                <div class="modal"><div class="modal-header"><h2>Lettre de motivation</h2><button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">×</button></div>
                <div style="padding:24px 28px;font-size:.9rem;line-height:1.8"><?= nl2br(View::e($app['cover_letter'])) ?></div></div>
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div></section>
