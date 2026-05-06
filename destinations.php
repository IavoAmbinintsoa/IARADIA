<?php

require_once __DIR__ . '/inc/functions.php';

$pageTitle = 'Destinations';


$pdo = getPDO();
$stmt = $pdo->query(
    'SELECT t.id_Trajet, t.distance_km_Trajet,
            v1.nom_Ville as from_city, v2.nom_Ville as to_city,
            MIN(ts.prix_Tarif_segment) as min_price
     FROM Trajet t
     JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
     JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
     LEFT JOIN Tarif_segment ts ON ts.id_Trajet = t.id_Trajet
     GROUP BY t.id_Trajet
     ORDER BY t.distance_km_Trajet DESC'
);
$allTrajets = $stmt->fetchAll() ?: [];

$heroRoutes = [
    ['from'=>'Antananarivo','to'=>'Toamasina',   'duration'=>'5h30', 'price'=>25000,
     'img'=>cityImg('toamasina'),
     'stops'=>['Antananarivo','Moramanga','Brickaville','Toamasina']],
    ['from'=>'Antananarivo','to'=>'Mahajanga',   'duration'=>'9h00', 'price'=>38000,
     'img'=>cityImg('mahajanga'),
     'stops'=>['Antananarivo','Maevatanana','Mahajanga']],
    ['from'=>'Antananarivo','to'=>'Fianarantsoa','duration'=>'6h00', 'price'=>28000,
     'img'=>cityImg('antsirabe'),
     'stops'=>['Antananarivo','Antsirabe','Ambositra','Fianarantsoa']],
];

$extraLines = [
    ['from'=>'Antananarivo','to'=>'Antsirabe',   'duration'=>'2h30','price'=>12000],
    ['from'=>'Antananarivo','to'=>'Morondava',   'duration'=>'8h00','price'=>35000],
    ['toamasina',  'to'=>'Antsirabe',    'duration'=>'7h00','price'=>32000],
    ['from'=>'Mahajanga',  'to'=>'Antsiranana',  'duration'=>'11h00','price'=>48000],
    ['from'=>'Fianarantsoa','to'=>'Toliara',     'duration'=>'5h00','price'=>22000],
    ['from'=>'Antananarivo','to'=>'Ambositra',   'duration'=>'4h00','price'=>18000],
];

include __DIR__ . '/templates/header.php';
?>

<style>
.dest-card { position:relative; border-radius:24px; overflow:hidden; border:1px solid rgba(255,255,255,0.1); transition:all 0.4s ease; }
.dest-card:hover { transform:translateY(-5px); border-color:rgba(212,165,116,0.4); }
.dest-bg { position:absolute; inset:0; background-size:cover; background-position:center; transition:transform 0.6s ease; }
.dest-card:hover .dest-bg { transform:scale(1.08); }
.dest-bg-overlay { position:absolute; inset:0; background:linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.85)); }
.dest-content { position:relative; z-index:10; min-height:360px; display:flex; flex-direction:column; justify-content:space-between; padding:2rem; }
</style>

<div class="min-h-screen py-16" style="background:var(--color-navy)">
  <div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-16">
      <p class="text-xs tracking-widest uppercase mb-3" style="color:var(--color-gold)">Explorez Madagascar</p>
      <h1 class="font-serif text-5xl md:text-6xl font-bold mb-4" style="color:var(--color-ivory)">Nos destinations</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <?php foreach ($heroRoutes as $r): ?>
        <div class="dest-card">
          <div class="dest-bg" style="background-image:url('<?= $r['img'] ?>')"></div>
          <div class="dest-bg-overlay"></div>
          <div class="dest-content">
            
            <div class="flex items-start justify-between">
              <div>
                <span class="text-[10px] text-white/40 uppercase tracking-widest font-medium">Départ</span>
                <p class="font-serif text-2xl text-white font-bold tracking-tight"><?= sanitize($r['from']) ?></p>
              </div>
              <div class="bg-white/10 backdrop-blur-md px-3 py-1 rounded-full border border-white/10">
                <span class="text-xs font-semibold tracking-wider text-gold"><?= $r['duration'] ?></span>
              </div>
            </div>

            <div class="py-4 flex flex-col items-center relative">
              <div class="absolute top-1/2 left-0 w-full border-t border-dashed -translate-y-1/2 border-gold/30 z-0"></div>
              <div class="flex justify-between w-full relative z-10 px-2">
                <?php foreach ($r['stops'] as $s): ?>
                  <div class="flex flex-col items-center">
                    <div class="w-2.5 h-2.5 rounded-full bg-gold border border-white/50 shadow-[0_0_8px_#D4A574]"></div>
                    <span class="text-[9px] text-white/70 mt-1 font-medium truncate max-w-[65px]"><?= sanitize($s) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="flex items-end justify-between">
              <div>
                <span class="text-[10px] text-white/40 uppercase tracking-widest">Arrivée</span>
                <p class="font-serif text-2xl text-white font-bold"><?= sanitize($r['to']) ?></p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <div class="text-right">
                  <span class="text-[10px] text-white/40 block">À partir de</span>
                  <span class="text-xl font-serif font-bold text-gold"><?= formatPrice($r['price']) ?></span>
                </div>
                <a href="/search.php?from=<?= urlencode($r['from']) ?>&to=<?= urlencode($r['to']) ?>&passengers=1"
                   class="px-5 py-1.5 rounded-full text-xs font-bold bg-gold text-slate-950 transition hover:opacity-90">
                  Réserver
                </a>
              </div>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>