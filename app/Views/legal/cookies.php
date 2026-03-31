<div class="page-header"><div class="page-header-inner"><h1>Politique de cookies</h1></div></div>
<div style="max-width:820px;margin:0 auto;padding:40px 4%;">
  <?php foreach([
    ['Cookie utilisé','<code style="background:rgba(255,255,255,.08);padding:2px 8px;border-radius:4px;">sc_session</code> — Cookie technique de session. Durée : fermeture du navigateur. HttpOnly + SameSite=Strict.'],
    ['Cookies tiers','StageConnect <strong>n\'utilise aucun cookie tiers</strong> : pas de tracking, pas d\'analytics externe, pas de publicité.'],
    ['Gérer vos cookies','Supprimez les cookies via les paramètres de votre navigateur. Note : cela vous déconnectera de la plateforme.'],
  ] as [$titre, $texte]): ?>
  <div style="background:#1a1a28;border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:24px 28px;margin-bottom:16px;">
    <h2 style="font-size:1rem;color:var(--accent);margin-bottom:10px;"><?= $titre ?></h2>
    <p style="color:rgba(255,255,255,.65);font-size:.88rem;line-height:1.8;"><?= $texte ?></p>
  </div>
  <?php endforeach; ?>
</div>
