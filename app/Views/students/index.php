<?php use App\Core\{View, Csrf}; ?>
<section class="page-header"><div class="page-header-inner"><h1>Étudiants</h1><p><?= $total ?> étudiant(s)</p></div></section>
<section class="page-body">
  <div class="main-content">
    <div class="top-bar">
      <form action="/students" method="GET" class="filter-bar">
        <input type="text" name="search" value="<?= View::e($search) ?>" placeholder="Nom, email…" class="form-input" style="max-width:300px" />
        <button type="submit" class="btn-primary">Rechercher</button>
        <a href="/students" class="btn-ghost">Reset</a>
      </form>
      <a href="/students/create" class="btn-primary">+ Ajouter un étudiant</a>
    </div>
    <div class="data-table-wrap">
      <table class="data-table">
        <thead><tr><th>Nom</th><th>Email</th><th>Pilote</th><th>Statut stage</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($students as $s): ?>
          <tr>
            <td><?= View::e($s['firstname'].' '.$s['lastname']) ?></td>
            <td><?= View::e($s['email']) ?></td>
            <td><?= View::e($s['promotion'] ?? '—') ?></td>
            <td><span class="status-badge status-<?= $s['stage_status'] ?>"><?= match($s['stage_status']){'searching'=>'🔍 Recherche','applied'=>'📬 Postulé','found'=>'✅ Trouvé','none'=>'—',default=>$s['stage_status']} ?></span></td>
            <td class="table-actions">
              <a href="/students/<?= $s['id'] ?>" class="btn-xs">👁 Voir</a>
              <a href="/students/<?= $s['id'] ?>/edit" class="btn-xs">✏️</a>
              <form action="/students/<?= $s['id'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?')"><?= Csrf::field() ?><button class="btn-xs btn-xs-danger">🗑</button></form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($pages>1): ?><nav class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
  </div>
</section>
