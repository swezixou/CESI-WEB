<?php use App\Core\{View, Csrf}; ?>

<section class="page-header">
  <div class="page-header-inner">
    <a href="/offers" class="back-link">← Retour aux offres</a>
    <h1><?= $offer ? 'Modifier l\'offre' : 'Nouvelle offre' ?></h1>
  </div>
</section>

<section class="page-body">
  <div class="main-content" style="max-width:740px">
    <div class="form-card">
      <form action="/offers/<?= $offer ? $offer['id'].'/edit' : 'create' ?>" method="POST" novalidate>
        <?= Csrf::field() ?>

        <div class="form-group">
          <label>Titre de l'offre *</label>
          <input type="text" name="title" required class="form-input"
                 value="<?= View::e($offer['title'] ?? '') ?>" placeholder="Développeur Web Full Stack" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Entreprise *</label>
            <select name="company_id" required class="form-input">
              <option value="">-- Sélectionner --</option>
              <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($offer['company_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                  <?= View::e($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Localisation</label>
            <input type="text" name="location" class="form-input"
                   value="<?= View::e($offer['location'] ?? '') ?>" placeholder="Paris, Lyon…" />
          </div>
        </div>

        <div class="form-group">
          <label>Description *</label>
          <textarea name="description" rows="6" required class="form-input"
                    placeholder="Décrivez le poste et les missions…"><?= View::e($offer['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Rémunération (€/mois)</label>
            <input type="number" name="salary" class="form-input" min="0" step="50"
                   value="<?= $offer['salary'] ?? '' ?>" placeholder="800" />
          </div>
          <div class="form-group">
            <label>Durée (semaines) *</label>
            <select name="duration" required class="form-input">
              <?php foreach ([8,12,16,20,24] as $d): ?>
                <option value="<?= $d ?>" <?= ($offer['duration'] ?? 0) == $d ? 'selected' : '' ?>><?= $d ?> sem.</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Date de l'offre</label>
            <input type="date" name="offer_date" class="form-input"
                   value="<?= View::e($offer['offer_date'] ?? date('Y-m-d')) ?>" />
          </div>
        </div>

        <div class="form-group">
          <label>Compétences requises</label>
          <div class="skills-checkboxes">
            <?php foreach ($skills as $skill): ?>
              <?php $checked = in_array($skill['id'], array_column($offer['skills'] ?? [], 'id')); ?>
              <label class="skill-check <?= $checked ? 'checked' : '' ?>">
                <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>"
                       <?= $checked ? 'checked' : '' ?> style="display:none" />
                <?= View::e($skill['label']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" <?= ($offer['is_active'] ?? 1) ? 'checked' : '' ?> />
            Offre active (visible par les étudiants)
          </label>
        </div>

        <div class="form-actions">
          <a href="/offers" class="btn-ghost">Annuler</a>
          <button type="submit" class="btn-primary"><?= $offer ? '💾 Enregistrer' : '✅ Créer l\'offre' ?></button>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
// Toggle skill checkboxes style
document.querySelectorAll('.skill-check').forEach(label => {
  label.addEventListener('click', function() {
    const cb = this.querySelector('input');
    cb.checked = !cb.checked;
    this.classList.toggle('checked', cb.checked);
  });
});
</script>
