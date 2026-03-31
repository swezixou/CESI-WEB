<?php use App\Core\{View, Csrf}; ?>
<section class="page-header page-header-admin"><div class="page-header-inner">
  <a href="/admin/pilots" class="back-link">← Retour</a>
  <h1><?= $pilot ? 'Modifier le pilote' : 'Nouveau pilote' ?></h1>
</div></section>
<section class="page-body"><div class="main-content" style="max-width:560px"><div class="form-card">
  <form action="/admin/pilots/<?= $pilot ? $pilot['id'].'/edit' : 'create' ?>" method="POST" novalidate>
    <?= Csrf::field() ?>
    <div class="form-row">
      <div class="form-group"><label>Prénom *</label><input type="text" name="firstname" required class="form-input" value="<?= View::e($pilot['firstname']??'') ?>" /></div>
      <div class="form-group"><label>Nom *</label><input type="text" name="lastname" required class="form-input" value="<?= View::e($pilot['lastname']??'') ?>" /></div>
    </div>
    <div class="form-group"><label>Email *</label><input type="email" name="email" required class="form-input" value="<?= View::e($pilot['email']??'') ?>" /></div>
    <?php if (!$pilot): ?>
    <div class="form-group"><label>Mot de passe * (8 min.)</label><input type="password" name="password" class="form-input" minlength="8" required /></div>
    <?php endif; ?>
    <div class="form-group"><label>Promotion</label><input type="text" name="promotion" class="form-input" value="<?= View::e($pilot['promotion']??'') ?>" placeholder="Promo 2025 – Informatique" /></div>
    <div class="form-actions">
      <a href="/admin/pilots" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-primary"><?= $pilot ? '💾 Enregistrer' : '✅ Créer le compte pilote' ?></button>
    </div>
  </form>
</div></div></section>
