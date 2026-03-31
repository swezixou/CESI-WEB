<?php use App\Core\{Auth, View, Csrf}; ?>

<section class="page-header">
  <div class="page-header-inner">
    <h1>Offres de stage</h1>
    <p><?= $total ?> offre<?= $total > 1 ? 's' : '' ?> disponible<?= $total > 1 ? 's' : '' ?></p>
  </div>
</section>

<section class="page-body">
  <aside class="sidebar">
    <form action="/offers" method="GET" id="searchForm">
      <div class="sidebar-section">
        <label class="sidebar-label">Mots-clés</label>
        <input type="text" name="search" value="<?= View::e($search) ?>" placeholder="Titre, entreprise…" class="form-input" />
      </div>
      <div class="sidebar-section">
        <label class="sidebar-label">Compétence</label>
        <select name="skill" class="form-input">
          <option value="">Toutes</option>
          <?php foreach ($skills as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $skillId == $s['id'] ? 'selected' : '' ?>><?= View::e($s['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="sidebar-section">
        <label class="sidebar-label">Durée (semaines)</label>
        <select name="duration" class="form-input">
          <option value="">Toutes</option>
          <option value="8"  <?= $duration == 8  ? 'selected':'' ?>>8 sem. (2 mois)</option>
          <option value="12" <?= $duration == 12 ? 'selected':'' ?>>12 sem. (3 mois)</option>
          <option value="16" <?= $duration == 16 ? 'selected':'' ?>>16 sem. (4 mois)</option>
          <option value="20" <?= $duration == 20 ? 'selected':'' ?>>20 sem. (5 mois)</option>
          <option value="24" <?= $duration == 24 ? 'selected':'' ?>>24 sem. (6 mois)</option>
        </select>
      </div>
      <button type="submit" class="btn-primary w-full">Filtrer</button>
      <a href="/offers" class="btn-ghost w-full" style="margin-top:8px;text-align:center;display:block">Réinitialiser</a>
    </form>

    <?php if (Auth::is('admin') || Auth::is('pilot')): ?>
      <div class="sidebar-section" style="margin-top:24px;">
        <a href="/offers/create" class="btn-primary w-full" style="text-align:center;display:block">+ Nouvelle offre</a>
        <a href="/offers/stats" class="btn-ghost w-full" style="text-align:center;display:block;margin-top:8px">📊 Statistiques</a>
      </div>
    <?php endif; ?>
  </aside>

  <div class="main-content">
    <?php if (empty($offers)): ?>
      <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>Aucune offre trouvée</h3>
        <p>Essayez d'autres critères de recherche.</p>
      </div>
    <?php else: ?>
      <div class="offers-grid">
        <?php foreach ($offers as $offer): ?>
        <div class="offer-card">
          <div class="offer-card-header">
            <div class="company-logo-sm"><?= strtoupper(substr($offer['company_name'], 0, 2)) ?></div>
            <?php if (Auth::is('student')): ?>
              <form action="/wishlist/<?= $offer['id'] ?>/add" method="POST" style="display:inline">
                <?= Csrf::field() ?>
                <button type="submit" class="wish-btn" title="Ajouter à la wish-list">🤍</button>
              </form>
            <?php endif; ?>
          </div>
          <a href="/offers/<?= $offer['id'] ?>" class="offer-title"><?= View::e($offer['title']) ?></a>
          <div class="offer-company"><?= View::e($offer['company_name']) ?> · <?= View::e($offer['city'] ?? '') ?></div>
          <div class="offer-tags">
            <span class="offer-tag tag-blue"><?= $offer['duration'] ?> sem.</span>
            <span class="offer-tag tag-green"><?= $offer['applicant_count'] ?> candidat(s)</span>
          </div>
          <div class="offer-footer">
            <span class="offer-meta"><?= $offer['offer_date'] ? date('d/m/Y', strtotime($offer['offer_date'])) : '' ?></span>
            <span class="offer-salary"><?= $offer['salary'] ? number_format($offer['salary'],0,',',' ').' €/mois' : 'N/A' ?></span>
          </div>
          <?php if (Auth::is('admin') || Auth::is('pilot')): ?>
            <div class="card-actions">
              <a href="/offers/<?= $offer['id'] ?>/edit" class="btn-xs">✏️ Modifier</a>
              <form action="/offers/<?= $offer['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer cette offre ?')">
                <?= Csrf::field() ?>
                <button class="btn-xs btn-xs-danger">🗑 Supprimer</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- SFx 27 – Pagination -->
      <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Pagination">
        <?php if ($page > 1): ?>
          <a href="?search=<?= urlencode($search) ?>&skill=<?= $skillId ?>&duration=<?= $duration ?>&page=<?= $page-1 ?>" class="page-btn">‹ Préc.</a>
        <?php endif; ?>
        <?php for ($i = max(1, $page-2); $i <= min($pages, $page+2); $i++): ?>
          <a href="?search=<?= urlencode($search) ?>&skill=<?= $skillId ?>&duration=<?= $duration ?>&page=<?= $i ?>"
             class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a href="?search=<?= urlencode($search) ?>&skill=<?= $skillId ?>&duration=<?= $duration ?>&page=<?= $page+1 ?>" class="page-btn">Suiv. ›</a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
