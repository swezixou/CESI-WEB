<?php use App\Core\{View, Csrf}; ?>
<section class="page-header"><div class="page-header-inner">
  <a href="/students" class="back-link">← Retour</a>
  <h1><?= $student ? 'Modifier l\'étudiant' : 'Nouvel étudiant' ?></h1>
</div></section>
<section class="page-body"><div class="main-content" style="max-width:640px">
  <div class="form-card">
    <form action="/students/<?= $student ? $student['id'].'/edit' : 'create' ?>" method="POST" novalidate>
      <?= Csrf::field() ?>
      <div class="form-row">
        <div class="form-group"><label>Prénom *</label><input type="text" name="firstname" required class="form-input" value="<?= View::e($student['firstname']??'') ?>" /></div>
        <div class="form-group"><label>Nom *</label><input type="text" name="lastname" required class="form-input" value="<?= View::e($student['lastname']??'') ?>" /></div>
      </div>
      <div class="form-group"><label>Email *</label><input type="email" name="email" required class="form-input" value="<?= View::e($student['email']??'') ?>" /></div>
      <?php if (!$student): ?>
      <div class="form-group"><label>Mot de passe (8 min.)</label><input type="password" name="password" class="form-input" minlength="8" /></div>
      <?php endif; ?>
      <div class="form-group">
        <label>Pilote de promotion</label>
        <select name="pilot_id" class="form-input">
          <option value="">— Aucun —</option>
          <?php foreach ($pilots as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($student['pilot_id']??0)==$p['id']?'selected':'' ?>><?= View::e($p['firstname'].' '.$p['lastname']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if ($student): ?>
      <div class="form-group">
        <label>Statut de recherche</label>
        <select name="stage_status" class="form-input">
          <option value="searching" <?= ($student['stage_status']??'')==='searching'?'selected':'' ?>>🔍 En recherche</option>
          <option value="applied"   <?= ($student['stage_status']??'')==='applied'?'selected':'' ?>>📬 Candidature envoyée</option>
          <option value="found"     <?= ($student['stage_status']??'')==='found'?'selected':'' ?>>✅ Stage trouvé</option>
          <option value="none"      <?= ($student['stage_status']??'')==='none'?'selected':'' ?>>— Non concerné</option>
        </select>
      </div>
      <?php endif; ?>
      <div class="form-actions">
        <a href="/students" class="btn-ghost">Annuler</a>
        <button type="submit" class="btn-primary"><?= $student ? '💾 Enregistrer' : '✅ Créer le compte' ?></button>
      </div>
    </form>
  </div>
</div></section>
