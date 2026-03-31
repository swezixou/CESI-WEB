<div class="page-header"><div class="page-header-inner"><h1>Mentions légales</h1><p>Conformément à la loi n°2004-575 du 21 juin 2004.</p></div></div>
<div style="max-width:820px;margin:0 auto;padding:40px 4%;">
  <?php foreach([
    ['Éditeur','<strong>Web4All</strong> – CESI École d\'Ingénieurs<br>30 Rue Cambronne, 75015 Paris<br>Email : <a href="mailto:web4all@cesi.fr">web4all@cesi.fr</a>'],
    ['Hébergement','Serveur Apache – Infrastructure CESI (usage pédagogique)'],
    ['Propriété intellectuelle','Tout le contenu est la propriété de Web4All–CESI. Reproduction interdite sans autorisation.'],
    ['Données personnelles','Conformément au RGPD. Contact : <a href="mailto:web4all@cesi.fr">web4all@cesi.fr</a>'],
    ['Responsabilité','StageConnect est une plateforme pédagogique. Les informations sont données à titre indicatif.'],
  ] as [$titre, $texte]): ?>
  <div style="background:#1a1a28;border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:24px 28px;margin-bottom:16px;">
    <h2 style="font-size:1rem;color:var(--accent);margin-bottom:10px;"><?= $titre ?></h2>
    <p style="color:rgba(255,255,255,.65);font-size:.88rem;line-height:1.8;"><?= $texte ?></p>
  </div>
  <?php endforeach; ?>
</div>
