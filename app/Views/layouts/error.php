<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= $code ?? 404 ?> – <?= htmlspecialchars($title ?? 'Erreur') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/app.css" />
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0D0D14;text-align:center;padding:40px">
  <div>
    <div style="font-size:5rem;margin-bottom:16px"><?= $code === 404 ? '🔍' : ($code === 403 ? '🔒' : '⚠️') ?></div>
    <h1 style="font-family:Syne,sans-serif;font-size:5rem;font-weight:800;color:#4AE68A;margin:0"><?= $code ?? 404 ?></h1>
    <h2 style="font-family:Syne,sans-serif;color:#fff;font-size:1.5rem;margin:12px 0 20px"><?= htmlspecialchars($title ?? 'Page introuvable') ?></h2>
    <p style="color:rgba(255,255,255,.5);margin-bottom:32px"><?= htmlspecialchars($message ?? 'La page que vous cherchez n\'existe pas ou a été déplacée.') ?></p>
    <a href="/" style="display:inline-block;background:#4AE68A;color:#0D0D14;padding:12px 28px;border-radius:10px;font-weight:700;text-decoration:none">← Retour à l'accueil</a>
  </div>
</div>
</body>
</html>
