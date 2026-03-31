<?php use App\Core\{View, Csrf}; ?>
<section class="page-header page-header-admin"><div class="page-header-inner"><h1>Pilotes de promotion</h1><p><?= $total ?> pilote(s)</p></div></section>
<section class="page-body"><div class="main-content">
  <div class="top-bar">
    <form action="/admin/pilots" method="GET" class="filter-bar">
      <input type="text" name="search" value="<?= View::e($search) ?>" placeholder="Nom…" class="form-input" style="max-width:280px" />
      <button type="submit" class="btn-primary">Rechercher</button>
      <a href="/admin/pilots" class="btn-ghost">Reset</a>
    </form>
    <a href="/admin/pilots/create" class="btn-primary">+ Nouveau pilote</a>
  </div>
  <div class="data-table-wrap">
    <table class="data-table">
      <thead><tr><th>Nom</th><th>Email</th><th>Promotion</th><th>Étudiants</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($pilots as $p): ?>
        <tr>
          <td><?= View::e($p['firstname'].' '.$p['lastname']) ?></td>
          <td><?= View::e($p['email']) ?></td>
          <td><?= View::e($p['promotion']??'—') ?></td>
          <td><?= $p['student_count'] ?></td>
          <td class="table-actions">
            <a href="/admin/pilots/<?= $p['id'] ?>" class="btn-xs">👁 Voir</a>
            <a href="/admin/pilots/<?= $p['id'] ?>/edit" class="btn-xs">✏️</a>
            <form action="/admin/pilots/<?= $p['id'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce pilote ?')"><?= Csrf::field() ?><button class="btn-xs btn-xs-danger">🗑</button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages>1): ?><nav class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
</div></section>
