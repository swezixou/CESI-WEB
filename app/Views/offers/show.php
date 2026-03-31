<?php use App\Core\{Auth, View, Csrf}; ?>

<section class="page-header">
  <div class="page-header-inner">
    <a href="/offers" class="back-link">← Retour aux offres</a>
    <h1><?= View::e($offer['title']) ?></h1>
    <p><?= View::e($offer['company_name']) ?> · <?= View::e($offer['city'] ?? '') ?></p>
  </div>
</section>

<section class="page-body">
  <div class="main-content">
    <div class="offer-detail-card">
      <div class="offer-detail-header">
        <div class="company-logo-lg"><?= strtoupper(substr($offer['company_name'], 0, 2)) ?></div>
        <div>
          <h2><?= View::e($offer['title']) ?></h2>
          <p class="offer-company"><?= View::e($offer['company_name']) ?></p>
          <div class="offer-tags" style="margin-top:8px">
            <span class="offer-tag tag-blue">📍 <?= View::e($offer['city'] ?? 'Non précisé') ?></span>
            <span class="offer-tag tag-green">⏱ <?= $offer['duration'] ?> semaines</span>
            <span class="offer-tag tag-orange">💰 <?= $offer['salary'] ? number_format($offer['salary'],0,',',' ').' €/mois' : 'Non précisé' ?></span>
            <span class="offer-tag tag-purple">👥 <?= $offer['applicant_count'] ?> candidat(s)</span>
          </div>
        </div>
      </div>

      <div class="offer-detail-body">
        <h3>Description du poste</h3>
        <p><?= nl2br(View::e($offer['description'])) ?></p>

        <?php if (!empty($offer['skills'])): ?>
        <h3>Compétences requises</h3>
        <div class="offer-tags">
          <?php foreach ($offer['skills'] as $skill): ?>
            <span class="offer-tag tag-green"><?= View::e($skill['label']) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h3>Informations</h3>
        <div class="info-grid">
          <div class="info-item"><strong>Entreprise</strong><?= View::e($offer['company_name']) ?></div>
          <div class="info-item"><strong>Email contact</strong><?= View::e($offer['company_email'] ?? '-') ?></div>
          <div class="info-item"><strong>Téléphone</strong><?= View::e($offer['company_phone'] ?? '-') ?></div>
          <div class="info-item"><strong>Date de l'offre</strong><?= $offer['offer_date'] ? date('d/m/Y', strtotime($offer['offer_date'])) : '-' ?></div>
          <div class="info-item"><strong>Localisation</strong><?= View::e($offer['city'] ?? '-') ?></div>
          <div class="info-item"><strong>Rémunération</strong><?= $offer['salary'] ? number_format($offer['salary'],0,',',' ').' €/mois' : 'Non précisé' ?></div>
        </div>
      </div>

      <!-- Actions -->
      <div class="offer-detail-actions">
        <?php if (Auth::is('student')): ?>
          <?php if ($hasApplied): ?>
            <div class="badge-applied">✅ Vous avez déjà postulé</div>
          <?php else: ?>
            <button class="btn-primary" onclick="document.getElementById('applyModal').style.display='flex'">
              📩 Postuler maintenant
            </button>
          <?php endif; ?>

          <?php if ($isWishlisted): ?>
            <form action="/wishlist/<?= $offer['id'] ?>/remove" method="POST">
              <?= Csrf::field() ?>
              <button class="btn-ghost">❤️ Dans ma wish-list</button>
            </form>
          <?php else: ?>
            <form action="/wishlist/<?= $offer['id'] ?>/add" method="POST">
              <?= Csrf::field() ?>
              <button class="btn-ghost">🤍 Ajouter à ma wish-list</button>
            </form>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (Auth::is('admin') || Auth::is('pilot')): ?>
          <a href="/offers/<?= $offer['id'] ?>/edit" class="btn-primary">✏️ Modifier</a>
          <form action="/offers/<?= $offer['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer ?')">
            <?= Csrf::field() ?>
            <button class="btn-ghost" style="color:#ef4444;border-color:#ef4444">🗑 Supprimer</button>
          </form>
        <?php endif; ?>

        <a href="/companies/<?= $offer['company_id'] ?>" class="btn-ghost">🏢 Voir l'entreprise</a>
      </div>
    </div>
  </div>
</section>

<!-- MODAL CANDIDATURE -->
<?php if (Auth::is('student') && !$hasApplied): ?>
<div class="modal-overlay" id="applyModal" style="display:none">
  <div class="modal">
    <div class="modal-header">
      <h2>Postuler – <?= View::e($offer['title']) ?></h2>
      <button onclick="document.getElementById('applyModal').style.display='none'" class="modal-close">×</button>
    </div>
    <form action="/offers/<?= $offer['id'] ?>/apply" method="POST" enctype="multipart/form-data" class="modal-form" novalidate id="applyForm">
      <?= Csrf::field() ?>
      <div class="form-group">
        <label>CV <small style="color:#999">(PDF, max 5 Mo – optionnel)</small></label>
        <input type="file" name="cv" accept=".pdf" class="form-input" />
      </div>
      <div class="form-group">
        <label>Lettre de motivation <small>(50 caractères min.)</small></label>
        <textarea name="cover_letter" rows="8" class="form-input" required minlength="50"
          placeholder="Madame, Monsieur, Je vous adresse ma candidature pour le poste de…"></textarea>
        <span id="charCount" style="font-size:.8rem;color:#999">0 caractères</span>
      </div>
      <div class="modal-actions">
        <button type="button" onclick="document.getElementById('applyModal').style.display='none'" class="btn-ghost">Annuler</button>
        <button type="submit" class="btn-primary">Envoyer ma candidature →</button>
      </div>
    </form>
  </div>
</div>
<script>
const ta = document.querySelector('textarea[name="cover_letter"]');
if (ta) ta.addEventListener('input', function() {
  document.getElementById('charCount').textContent = this.value.length + ' caractères';
});
</script>
<?php endif; ?>
