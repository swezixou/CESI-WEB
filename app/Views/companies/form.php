<?php use App\Core\{View, Csrf}; ?>
<section class="page-header"><div class="page-header-inner">
  <a href="/companies" class="back-link">← Retour</a>
  <h1><?= $company ? 'Modifier l\'entreprise' : 'Nouvelle entreprise' ?></h1>
</div></section>
<section class="page-body"><div class="main-content" style="max-width:680px"><div class="form-card">
  <form action="/companies/<?= $company ? $company['id'].'/edit' : 'create' ?>" method="POST" novalidate>
    <?= Csrf::field() ?>
    <div class="form-group"><label>Nom *</label><input type="text" name="name" required class="form-input" value="<?= View::e($company['name']??'') ?>" /></div>
    <div class="form-group"><label>Description</label><textarea name="description" rows="4" class="form-input"><?= View::e($company['description']??'') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Email *</label><input type="email" name="email" required class="form-input" value="<?= View::e($company['email']??'') ?>" /></div>
      <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" class="form-input" value="<?= View::e($company['phone']??'') ?>" placeholder="0X XX XX XX XX" /></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Ville</label><input type="text" name="city" class="form-input" value="<?= View::e($company['city']??'') ?>" /></div>
      <div class="form-group"><label>Secteur</label><input type="text" name="sector" class="form-input" value="<?= View::e($company['sector']??'') ?>" placeholder="Développement web, Data…" /></div>
    </div>
    <div class="form-actions">
      <a href="/companies" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-primary"><?= $company ? '💾 Enregistrer' : '✅ Créer' ?></button>
    </div>
  </form>
</div></div></section>
