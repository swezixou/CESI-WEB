<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= \App\Core\View::e($pageTitle ?? APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/app.css" />
</head>
<body class="auth-body">

<?php $flashes = \App\Core\Flash::get(); ?>
<?php if ($flashes): ?>
  <div class="flash-container" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;width:min(480px,90vw)">
    <?php foreach ($flashes as $flash): ?>
      <div class="flash flash-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?= $content ?>

<script src="/assets/js/app.js"></script>
</body>
</html>
