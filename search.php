<?php
require_once __DIR__ . '/inc/functions.php';
$pageTitle = 'Résultats de recherche';

$from       = sanitize(trim($_GET['from']       ?? ''));
$to         = sanitize(trim($_GET['to']         ?? ''));
$date       = sanitize(trim($_GET['date']       ?? ''));
$passengers = max(1, min(8, (int)($_GET['passengers'] ?? 1)));

$results = searchVoyages("Antananarivo","Soavinandriana",null);
/* echo '<pre>';
print("from " . $from . "\n");
print("to " . $to . "\n");
var_dump($results);
echo '</pre>';
die(); */




include __DIR__ . '/templates/header.php';
?>
<div class="min-h-screen py-12" style="background:var(--color-navy)">
  <div class="max-w-4xl mx-auto px-6">

    <div class="animate-fade-up mb-8">
      <?php if ($date): ?>
        <p class="text-xs mb-1" style="color:rgba(245,241,235,0.4)"><?= $date ?> · <?= $passengers ?> passager<?= $passengers > 1 ? 's' : '' ?></p>
      <?php endif; ?>
      <?php if ($from && $to): ?>
        <h1 class="font-serif text-3xl md:text-4xl font-bold" style="color:var(--color-ivory)">
          <?= sanitize($from) ?> <span style="color:var(--color-gold)">→</span> <?= sanitize($to) ?>
        </h1>
      <?php else: ?>
        <h1 class="font-serif text-3xl md:text-4xl font-bold" style="color:var(--color-ivory)">Tous les voyages</h1>
      <?php endif; ?>
    </div>

    <?php if (count($results) > 0): ?>
    <?php
      $nombreDispo = count($results);
    ?>

    <!-- Resultat -->
    <div class="flex flex-col gap-2.5" id="results-list">
      <?php 
      foreach ($results as $v):
        $tarif  = getTarifForVoyage((int)$v['id_Voyage']);
        $price  = $tarif ? (float)$tarif['prix_Tarif_segment'] : 0;
        $st     = getVoyageStats($v);
        // debug($st); 

        $libre  = $st['libre'];
        $total  = $st['total'];
        $taken  = $st['taken'];
        $pct    = $total > 0 ? round(($taken / $total) * 100) : 0;
        $heure  = date('H:i', strtotime($v['date_depart_Voyage']));
        $dateD  = date('Y-m-d', strtotime($v['date_depart_Voyage']));

        if ($libre === 0)       $detailVoyage = 'Complet';
        elseif ($libre <= 5)    $detailVoyage = 'Presque complet';
        else                    $detailVoyage = 'Disponible';

        $statusColors = [
          'Complet'        => ['color'=>'#E24B4A'],
          'Presque complet'=> ['color'=>'#EFA826'],
          'Disponible'     => ['color'=>'#1DA06E'],
        ];
        $sc = $statusColors[$detailVoyage];
        $barColor = $pct >= 100 ? '#E24B4A' : ($pct >= 80 ? '#EFA826' : '#1DA06E');
        $isFull = false;
        if($libre <= 0)$isFull = true;

      ?>
      <div class="voyage-card flex flex-col md:flex-row md:items-center gap-5 p-5 transition-all duration-200 "
           data-status="<?= $detailVoyage ?>"
           style="background:rgba(245,241,235,0.04);<?= $isFull ? 'opacity:0.45' : '' ?>">

        <div class="flex-shrink-0 text-center min-w-[52px]">
          <div class="text-2xl font-medium" style="color:var(--color-ivory)"><?= $heure ?></div>
          <div class="text-xs mt-0.5" style="color:rgba(245,241,235,0.35)"><?= $dateD ?></div>
        </div>

        <div class="w-px h-10 hidden md:block flex-shrink-0" style="background:rgba(245,241,235,0.08)"></div>

        <!-- A -> B  -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-2">
            <span class="text-sm font-medium" style="color:var(--color-ivory)"><?= sanitize($v['from_city']) ?></span>
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--color-gold)">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
            <span class="text-sm font-medium" style="color:var(--color-ivory)"><?= sanitize($v['to_city']) ?></span>
          </div>
        </div>

        <!-- statut -->
        <span class="self-start md:self-center px-3 py-1 rounded-full text-xs font-medium flex-shrink-0"
              style="color:<?= $sc['color'] ?>;">
          <?= $detailVoyage ?>
        </span>

        <!-- Prix -->
        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
          <span class="text-xl font-medium" style="color:var(--color-ivory)"><?= $price ? formatPrice($price) : '—' ?></span>
          <?php if (!$isFull && $price): ?>
            <a href="/reservation.php?voyageId=<?= $v['id_Voyage'] ?>&from=<?= urlencode($v['from_city']) ?>&to=<?= urlencode($v['to_city']) ?>&date=<?= urlencode($dateD) ?>&passengers=<?= $passengers ?>&price=<?= $price ?>&vehicle=<?= urlencode($v['immatriculation_Vehicule'] ?? '') ?>&heure=<?= urlencode($heure) ?>"
               class="px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200"
               style="color:var(--color-gold);background:transparent">
              Réserver
            </a>
          <?php else: ?>
            <button disabled class="px-4 py-1.5 rounded-full text-xs cursor-not-allowed"
                    style="border:0.5px solid rgba(245,241,235,0.08);color:rgba(245,241,235,0.2)">
              <?= $isFull ? 'Complet' : 'Indisponible' ?>
            </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php else: ?>
    <div class="text-center py-24">
      <p class="text-sm mb-3" style="color:rgba(245,241,235,0.3)">
        <?= ($from && $to) ? 'Aucun voyage trouvé pour ce trajet.' : "Lancez une recherche depuis la page d'accueil." ?>
      </p>
      <a href="/index.php" class="text-xs hover:underline" style="color:var(--color-gold)">← Retour à l'accueil</a>
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
  function filterVoyages(f) {
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
      var sel = btn.dataset.filter === f;
      btn.style.borderColor = sel ? 'rgba(212,165,116,0.4)' : 'rgba(245,241,235,0.12)';
      btn.style.color       = sel ? 'var(--color-gold)'     : 'rgba(245,241,235,0.4)';
    });
    document.querySelectorAll('.voyage-card').forEach(function(card) {
      card.style.display = (f === 'Tous' || card.dataset.status === f) ? '' : 'none';
    });
  }
  filterVoyages('Tous');
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>