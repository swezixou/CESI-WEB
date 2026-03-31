<div class="page-header"><div class="page-header-inner"><h1>Politique de confidentialité</h1><p>Dernière mise à jour : <?= date('d/m/Y') ?></p></div></div>
<div style="max-width:820px;margin:0 auto;padding:40px 4%;">
  <?php foreach([
    ['Données collectées','Nom, prénom, email (identification). Rôle utilisateur. Lettres de motivation et CV uploadés. Logs de navigation.'],
    ['Finalité','Permettre la connexion et l\'accès à votre espace. Mettre en relation étudiants et entreprises. Permettre aux pilotes de suivre leurs promotions.'],
    ['Sécurité','Les mots de passe sont <strong>hashés en bcrypt</strong>. Aucun mot de passe stocké en clair. Cookies : HttpOnly, SameSite=Strict.'],
    ['Conservation','Durée de votre formation CESI + 12 mois maximum.'],
    ['Vos droits','Accès, rectification, suppression, opposition. Contact : <a href="mailto:web4all@cesi.fr">web4all@cesi.fr</a>'],
  ] as [$titre, $texte]): ?>
  <div style="background:#1a1a28;border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:24px 28px;margin-bottom:16px;">
    <h2 style="font-size:1rem;color:var(--accent);margin-bottom:10px;"><?= $titre ?></h2>
    <p style="color:rgba(255,255,255,.65);font-size:.88rem;line-height:1.8;"><?= $texte ?></p>
  </div>
  <?php endforeach; ?>
</div>
