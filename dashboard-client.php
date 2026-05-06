<?php
require_once __DIR__ . '/inc/functions.php';
require_login();

$pageTitle = 'Mon compte';
$user      = current_user();


$trips  = getReservationsByUser((int)$user['id']);
$upcoming = array_filter($trips, fn($t) => strtotime($t['date_depart_Voyage']) >= strtotime('today'));

include __DIR__ . '/templates/header.php';
?>

<div class="min-h-screen py-0" style="background:var(--color-navy)">
  <!-- avatar -->
  <div class="relative mb-20">
    <div class="h-52 w-full" style="background:linear-gradient(135deg,rgba(212,165,116,0.25) 0%,rgba(212,165,116,0.05) 100%)"></div>
    <div class="max-w-3xl mx-auto px-6 relative">
      <div class="absolute -top-14 left-6 flex items-end gap-5">
        <div class="w-28 h-28 rounded-full flex items-center justify-center text-4xl font-serif font-bold flex-shrink-0"
             style="background:rgba(212,165,116,0.15);border:3px solid var(--color-navy);color:var(--color-gold);box-shadow:0 0 0 2px rgba(212,165,116,0.3)">
          <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <div class="pb-2">
          <h1 class="text-2xl font-serif font-bold leading-tight" style="color:var(--color-ivory)"><?= sanitize($user['name']) ?></h1>
          <p class="text-sm mt-0.5" style="color:rgba(212,165,116,0.5)"><?= sanitize($user['email']) ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-3xl mx-auto px-6 space-y-5">
    <!-- ho avy -->
    <?php if ($upcoming): ?>
    <div class="overflow-hidden rounded-2xl" style="border:1px solid rgba(212,165,116,0.15)">
      <div class="px-5 py-4 flex items-center justify-between" style="background:rgba(212,165,116,0.06);border-bottom:1px solid rgba(212,165,116,0.1)">
        <h2 class="font-serif font-semibold" style="color:var(--color-ivory)">Prochains voyages</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium"
              style="background:rgba(212,165,116,0.15);color:var(--color-gold);border:1px solid rgba(212,165,116,0.25)">
          <?= count($upcoming) ?>
        </span>
      </div>
      <?php foreach ($upcoming as $trip):
        $heure = date('H:i', strtotime($trip['date_depart_Voyage']));
      ?>
      <div class="flex items-center justify-between px-5 py-4 transition-colors"
           style="border-bottom:1px solid rgba(212,165,116,0.07)"
           data-trip-id="<?= $trip['id_Reservation'] ?>">
        <div class="flex items-center gap-4">
          <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
               style="background:rgba(212,165,116,0.1)">
            <svg class="w-4 h-4" style="color:var(--color-gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-medium" style="color:var(--color-ivory)">
              <?= sanitize($trip['from_city']) ?> → <?= sanitize($trip['to_city']) ?>
            </p>
            <p class="text-xs mt-0.5" style="color:rgba(212,165,116,0.45)"><?= sanitize($trip['date_depart_Voyage'] ?? '') ?> · <?= sanitize($heure) ?></p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span class="font-serif text-base font-bold" style="color:var(--color-gold)"><?= formatPrice((float)$trip['total_prix_Reservation']) ?></span>
          <button onclick="showQR(<?= $trip['id_Reservation'] ?>,<?= json_encode($trip['from_city'].'→'.$trip['to_city']) ?>,<?= json_encode($trip['QR_code_Reservation'] ?? '') ?>)"
                  class="text-xs px-3 py-1.5 rounded-full transition-colors"
                  style="border:1px solid rgba(212,165,116,0.25);color:var(--color-gold);background:rgba(212,165,116,0.06)">
            Voir QR
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    
    <div class="pb-10 text-center pt-80">
      <a href="/search.php"
         class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-semibold hover:opacity-90 transition-all glow-gold"
         style="background:var(--color-gold);color:#F5F1EB">
        Réserver un nouveau voyage
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
      </a>
    </div>

  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
