<?php use App\Core\{Auth, View, Csrf}; ?>
<section class="page-header">
  <div class="page-header-inner"><h1>Entreprises</h1><p><?= $total ?> entreprise(s)</p></div>
</section>
<section class="page-body">
  <aside class="sidebar">
    <form action="/companies" method="GET">
      <div class="sidebar-section">
        <label class="sidebar-label">Recherche</label>
        <input type="text" name="search" value="<?= View::e($search) ?>" placeholder="Nom…" class="form-input" />
      </div>
      <div class="sidebar-section">
        <label class="sidebar-label">Secteur</label>
        <select name="sector" class="form-input">
          <option value="">Tous</option>
          <?php foreach ($sectors as $s): ?><option value="<?= View::e($s['sector']) ?>" <?= $sector===$s['sector']?'selected':'' ?>><?= View::e($s['sector']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-primary w-full">Filtrer</button>
      <a href="/companies" class="btn-ghost w-full" style="margin-top:8px;text-align:center;display:block">Reset</a>
    </form>
    <?php if (Auth::is('admin') || Auth::is('pilot')): ?>
      <div style="margin-top:20px"><a href="/companies/create" class="btn-primary w-full" style="text-align:center;display:block">+ Nouvelle entreprise</a></div>
    <?php endif; ?>
  </aside>
  <div class="main-content">
    <?php if (empty($companies)): ?>
      <div class="empty-state"><div class="empty-icon">🏢</div><h3>Aucune entreprise trouvée</h3></div>
    <?php else: ?>
      <div class="companies-grid">
        <?php foreach ($companies as $c): ?>
        <a href="/companies/<?= $c['id'] ?>" class="company-card-full">
          <div class="company-logo-sm" style="margin-bottom:14px"><?= strtoupper(substr($c['name'],0,2)) ?></div>
          <div class="company-name"><?= View::e($c['name']) ?></div>
          <div class="company-sector"><?= View::e($c['sector']??'') ?></div>
          <?php if ($c['avg_rating']): ?><div class="company-rating">★ <?= $c['avg_rating'] ?> (<?= $c['review_count'] ?> avis)</div><?php endif; ?>
          <div style="margin-top:8px;font-size:.78rem;color:#aaa"><?= $c['applicant_count'] ?> candidature(s)</div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php if ($pages>1): ?><nav class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><a href="?search=<?= urlencode($search) ?>&sector=<?= urlencode($sector) ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
    <?php endif; ?>
  </div>
</section>
