<?php use App\Core\{View, Csrf}; ?>
<section class="page-header"><div class="page-header-inner"><h1>Ma wish-list</h1><p><?= count($offers) ?> offre(s)</p></div></section>
<section class="page-body"><div class="main-content">
  <?php if (empty($offers)): ?>
    <div class="empty-state"><div class="empty-icon">🤍</div><h3>Wish-list vide</h3><a href="/offers" class="btn-primary">Parcourir →</a></div>
  <?php else: ?>
    <div class="offers-grid">
      <?php foreach ($offers as $offer): ?>
      <div class="offer-card">
        <div class="offer-card-header">
          <div class="company-logo-sm"><?= strtoupper(substr($offer['company_name'],0,2)) ?></div>
          <form action="/wishlist/<?= $offer['id'] ?>/remove" method="POST"><?= Csrf::field() ?><button class="wish-btn active">❤️</button></form>
        </div>
        <a href="/offers/<?= $offer['id'] ?>" class="offer-title"><?= View::e($offer['title']) ?></a>
        <div class="offer-company"><?= View::e($offer['company_name']) ?></div>
        <div class="offer-footer"><span class="offer-salary"><?= $offer['salary']?number_format($offer['salary'],0,',',' ').' €/mois':'N/A' ?></span></div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div></section>
