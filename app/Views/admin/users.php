<?php use App\Core\{View, Csrf}; ?>
<section class="page-header page-header-admin">
  <div class="page-header-inner"><h1>Gestion des utilisateurs</h1><p><?= $total ?> compte(s)</p></div>
</section>
<section class="page-body">
  <div class="main-content">
    <form action="/admin/users" method="GET" class="filter-bar">
      <input type="text" name="search" value="<?= View::e($search) ?>" placeholder="Nom, email…" class="form-input" style="max-width:260px" />
      <select name="role" class="form-input" style="max-width:160px">
        <option value="">Tous les rôles</option>
        <option value="student" <?= $roleFilter==='student'?'selected':'' ?>>Étudiant</option>
        <option value="pilot"   <?= $roleFilter==='pilot'?'selected':'' ?>>Pilote</option>
        <option value="admin"   <?= $roleFilter==='admin'?'selected':'' ?>>Admin</option>
      </select>
      <button type="submit" class="btn-primary">Filtrer</button>
      <a href="/admin/users" class="btn-ghost">Reset</a>
    </form>
    <div class="data-table-wrap">
      <table class="data-table">
        <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Créé le</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr class="<?= !$u['is_active']?'row-inactive':'' ?>">
            <td><?= $u['id'] ?></td>
            <td><?= View::e($u['firstname'].' '.$u['lastname']) ?></td>
            <td><?= View::e($u['email']) ?></td>
            <td><span class="role-tag role-tag-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><span class="status-dot <?= $u['is_active']?'active':'inactive' ?>"></span><?= $u['is_active']?'Actif':'Désactivé' ?></td>
            <td><?= date('d/m/Y',strtotime($u['created_at'])) ?></td>
            <td class="table-actions">
              <form action="/admin/users/<?= $u['id'] ?>/toggle" method="POST" style="display:inline"><?= Csrf::field() ?><button class="btn-xs"><?= $u['is_active']?'🔒 Désactiver':'✅ Activer' ?></button></form>
              <?php if ($u['role']!=='admin'): ?>
              <form action="/admin/users/<?= $u['id'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?')"><?= Csrf::field() ?><button class="btn-xs btn-xs-danger">🗑</button></form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($pages>1): ?><nav class="pagination"><?php for ($i=1;$i<=$pages;$i++): ?><a href="?search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
  </div>
</section>
