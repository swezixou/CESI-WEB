<?php use App\Core\{Auth, View, Csrf}; ?>
<section class="page-header">
  <div class="page-header-inner">
    <a href="/companies" class="back-link">← Retour aux entreprises</a>
    <h1><?= View::e($company['name']) ?></h1>
    <p><?= View::e($company['sector']??'') ?> · <?= View::e($company['city']??'') ?></p>
  </div>
</section>
<section class="page-body">
  <div class="main-content">
    <div class="offer-detail-card" style="margin-bottom:24px">
      <div class="offer-detail-header">
        <div class="company-logo-lg"><?= strtoupper(substr($company['name'],0,2)) ?></div>
        <div>
          <h2><?= View::e($company['name']) ?></h2>
          <div class="offer-tags" style="margin-top:8px">
            <?php if($company['city']): ?><span class="offer-tag tag-blue">📍 <?= View::e($company['city']) ?></span><?php endif; ?>
            <?php if($company['sector']): ?><span class="offer-tag tag-green"><?= View::e($company['sector']) ?></span><?php endif; ?>
            <?php if($company['avg_rating']): ?><span class="offer-tag tag-orange">★ <?= $company['avg_rating'] ?> (<?= $company['review_count'] ?> avis)</span><?php endif; ?>
            <span class="offer-tag tag-purple">👥 <?= $company['applicant_count'] ?> candidature(s)</span>
          </div>
        </div>
      </div>
      <div class="offer-detail-body">
        <?php if ($company['description']): ?><p><?= nl2br(View::e($company['description'])) ?></p><?php endif; ?>
        <div class="info-grid" style="margin-top:20px">
          <div class="info-item"><strong>Email</strong><?= View::e($company['email']) ?></div>
          <div class="info-item"><strong>Téléphone</strong><?= View::e($company['phone']??'—') ?></div>
        </div>
      </div>
      <?php if (Auth::is('admin') || Auth::is('pilot')): ?>
      <div class="offer-detail-actions">
        <a href="/companies/<?= $company['id'] ?>/edit" class="btn-primary">✏️ Modifier</a>
        <form action="/companies/<?= $company['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer cette entreprise ?')">
          <?= Csrf::field() ?><button class="btn-ghost" style="color:#dc2626;border-color:#dc2626">🗑 Supprimer</button>
        </form>
      </div>
      <?php endif; ?>
    </div>

    <!-- Offres de l'entreprise -->
    <h2 style="margin-bottom:16px">Offres disponibles (<?= count($offers) ?>)</h2>
    <?php if (empty($offers)): ?>
      <div class="empty-state-sm">Aucune offre active pour cette entreprise.</div>
    <?php else: ?>
      <div class="offers-grid" style="margin-bottom:32px">
        <?php foreach ($offers as $o): ?>
        <a href="/offers/<?= $o['id'] ?>" class="offer-card">
          <div class="offer-title"><?= View::e($o['title']) ?></div>
          <div class="offer-tags"><span class="offer-tag tag-blue"><?= $o['duration'] ?> sem.</span></div>
          <div class="offer-footer">
            <span class="offer-meta">👥 <?= $o['applicant_count'] ?></span>
            <span class="offer-salary"><?= $o['salary']?number_format($o['salary'],0,',',' ').' €':'N/A' ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Évaluations -->
    <h2 style="margin-bottom:16px">Évaluations (<?= count($reviews) ?>)</h2>
    <?php if (!empty($reviews)): ?>
      <div class="list-cards" style="margin-bottom:24px">
        <?php foreach ($reviews as $r): ?>
        <div class="list-card" style="flex-direction:column;align-items:flex-start">
          <div style="display:flex;justify-content:space-between;width:100%">
            <strong><?= View::e($r['firstname'].' '.$r['lastname']) ?></strong>
            <span style="color:#f59e0b"><?= str_repeat('★',$r['rating']) ?><?= str_repeat('☆',5-$r['rating']) ?></span>
          </div>
          <?php if ($r['comment']): ?><p style="font-size:.88rem;color:#555;margin-top:6px"><?= View::e($r['comment']) ?></p><?php endif; ?>
          <small style="color:#aaa;font-size:.75rem"><?= date('d/m/Y',strtotime($r['created_at'])) ?></small>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Formulaire évaluation -->
    <?php if ($canReview): ?>
    <div class="form-card">
      <h3 style="margin-bottom:16px">Évaluer cette entreprise</h3>
      <form action="/companies/<?= $company['id'] ?>/review" method="POST">
        <?= Csrf::field() ?>
        <div class="form-group">
          <label>Note *</label>
          <div style="display:flex;gap:6px">
            <?php for ($i=1;$i<=5;$i++): ?>
              <label style="cursor:pointer;font-size:1.4rem">
                <input type="radio" name="rating" value="<?= $i ?>" required style="display:none" />⭐
              </label>
            <?php endfor; ?>
          </div>
        </div>
        <div class="form-group"><label>Commentaire</label><textarea name="comment" rows="4" class="form-input" placeholder="Partagez votre expérience…"></textarea></div>
        <button type="submit" class="btn-primary">Envoyer mon évaluation</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
</section>
