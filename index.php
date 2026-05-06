<?php

require_once __DIR__ . '/inc/functions.php';

$pageTitle = 'Accueil';

$cities = getCities();

$destinations = getDestinations();

// top 6
$popular = array_slice($destinations, 0, 6);

include __DIR__ . '/templates/header.php';
?>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
  <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
       style="background-image:url('<?= main_image() ?>')"></div>

  <div class="relative z-10 text-center px-6 w-full max-w-5xl mx-auto flex flex-col items-center gap-10">
    <div class="glass px-6 py-2 rounded-full text-xs uppercase tracking-[0.2em] animate-fade-up"
         style="border:1px solid rgba(239,168,38,0.22);color:var(--color-gold)">
      Madagascar · Excellence du Voyage
    </div>

    <div class="space-y-4">
      <h1 class="font-serif text-6xl md:text-8xl lg:text-9xl font-bold leading-[0.9] animate-fade-up tracking-tight"
          style="color:var(--color-ivory);animation-delay:0.1s">
        L'art de <br>
        <span class="italic" style="color:var(--color-gold);text-shadow:0 0 40px rgba(239,168,38,0.25)">voyager</span>
      </h1>
    </div>

<!-- REcherche -->
    <div class="w-full animate-fade-up mt-4" style="animation-delay:0.3s">
      <form action="/search.php" method="GET"
            class="glass rounded-[2rem] p-5 md:p-8 w-full max-w-4xl mx-auto shadow-2xl"
            style="border:1px solid rgba(55,138,221,0.15)">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <!-- depart -->
          <div class="flex flex-col gap-1">
            <label class="text-xs font-medium uppercase tracking-widest pl-1" style="color:rgba(245,241,235,0.4)">Départ</label>
            <select name="from" class="rounded-xl px-3 py-2.5 text-sm focus:outline-none transition-colors"
                    style="background:rgba(55,138,221,0.07);border:1px solid rgba(55,138,221,0.15);color:var(--color-ivory)">
              <option value="">Choisir…</option>
                <?php foreach ($cities as $c): ?>
                  <option value="<?= sanitize($c) ?>"><?= sanitize($c) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
        <!-- arrive -->
          <div class="flex flex-col gap-1">
            <label class="text-xs font-medium uppercase tracking-widest pl-1" style="color:rgba(245,241,235,0.4)">Arrivée</label>
            <select name="to" class="rounded-xl px-3 py-2.5 text-sm focus:outline-none transition-colors"
                    style="background:rgba(55,138,221,0.07);border:1px solid rgba(55,138,221,0.15);color:var(--color-ivory)">
              <option value="">Choisir…</option>
              <?php foreach ($cities as $c): ?>
                <option value="<?= sanitize($c) ?>"><?= sanitize($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
        <!-- date -->
          <div class="flex flex-col gap-1">
            <label class="text-xs font-medium uppercase tracking-widest pl-1" style="color:rgba(245,241,235,0.4)">Date</label>
            <input type="date" name="date" min="<?= date('Y-m-d') ?>"
                   class="rounded-xl px-3 py-2.5 text-sm focus:outline-none transition-colors"
                   style="background:rgba(55,138,221,0.07);border:1px solid rgba(55,138,221,0.15);color:var(--color-ivory)"/>
          </div>

        <!-- nombre passager -->
          <div class="flex flex-col gap-1">
            <label class="text-xs font-medium uppercase tracking-widest pl-1" style="color:rgba(245,241,235,0.4)">Passagers</label>
            <div class="flex items-center gap-2 rounded-xl px-3 py-2.5"
                 style="background:rgba(55,138,221,0.07);border:1px solid rgba(55,138,221,0.15)">
              <button type="button" onclick="stepPassenger(-1)" class="w-5 h-5 flex items-center justify-center text-lg leading-none transition-colors"
                      style="color:rgba(245,241,235,0.5)">−</button>
              <input type="number" name="passengers" id="passCount" value="1" min="1" max="8" readonly
                     class="flex-1 text-center text-sm bg-transparent border-none outline-none" style="color:var(--color-ivory)"/>
              <button type="button" onclick="stepPassenger(1)" class="w-5 h-5 flex items-center justify-center text-lg leading-none transition-colors"
                      style="color:rgba(245,241,235,0.5)">+</button>
            </div>
          </div>

        </div>

        <!-- boutton REcherche -->
        <div class="mt-5 flex justify-center">
          <button type="submit"
                  class="px-10 py-3 rounded-full font-semibold text-sm glow-gold hover:opacity-90 transition-all duration-300 flex items-center gap-2"
                  style="background:var(--color-gold);color:#F5F1EB">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Rechercher un voyage
          </button>
        </div>

      </form>
    </div>
  </div>
</section>

<section class="py-16">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-6">
      <p class="text-xs tracking-widest uppercase" style="color:rgba(245,241,235,0.3)">Trajets populaires</p>
      <a href="/search.php" class="text-xs hover:underline" style="color:var(--color-gold)">Voir tous</a>
    </div>
    <div class="flex gap-6 overflow-x-auto py-2 pb-6 -mx-6 px-6">
      <?php
        $bgImages = [
          'Antananarivo' => cityImg('antananarivo'),
          'Toamasina'    => cityImg('toamasina'),
          'Mahajanga'    => cityImg('mahajanga'),
          'Fianarantsoa' => cityImg('antsirabe'),
          'Toliara'      => cityImg('toliara'),
          'Antsirabe'    => cityImg('antsirabe'),
        ];

        $popularRoutes = [
          ['from'=>'Antananarivo','to'=>'Toamasina',   'duration'=>'5h30','price'=>25000],
          ['from'=>'Antananarivo','to'=>'Mahajanga',   'duration'=>'9h00','price'=>38000],
          ['from'=>'Antananarivo','to'=>'Antsirabe',   'duration'=>'2h30','price'=>12000],
          ['from'=>'Antananarivo','to'=>'Fianarantsoa','duration'=>'6h00','price'=>28000],
          ['from'=>'Toamasina',  'to'=>'Toliara',      'duration'=>'16h00','price'=>65000],
          ['from'=>'Mahajanga',  'to'=>'Antsirabe',    'duration'=>'11h00','price'=>48000],
        ];
        foreach ($popularRoutes as $r):
          $bg = $bgImages[$r['to']] ?? $bgImages['Antananarivo'];
      ?>
      <a href="/search.php?from=<?= urlencode($r['from']) ?>&to=<?= urlencode($r['to']) ?>&passengers=1"
         class="relative rounded-2xl min-w-[300px] md:min-w-[380px] h-52 md:h-64 overflow-hidden shadow-xl transform hover:-translate-y-2 transition-all duration-300 bg-cover bg-center flex-shrink-0"
         style="background-image:linear-gradient(180deg,rgba(4,16,31,.12),rgba(4,10,20,.5)),url(<?= $bg ?>)">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/10"></div>
        <div class="relative z-10 h-full flex flex-col justify-between p-5">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-xs uppercase tracking-widest" style="color:rgba(255,255,255,0.1)"><?= $r['duration'] ?></div>
              <h3 class="text-lg md:text-xl font-serif text-white font-semibold mt-1"><?= sanitize($r['from']) ?> → <?= sanitize($r['to']) ?></h3>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full"
                 style="background:rgba(239,168,38,0.18);border:1px solid rgba(239,168,38,0.3)">
              <span class="text-sm font-semibold" style="color:var(--color-gold)"><?= formatPrice($r['price']) ?></span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <div class="text-sm text-white/60">Places limitées · Réservez vite</div>
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold"
                  style="background:var(--color-gold);color:#F5F1EB">Réserver</span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="max-w-7xl mx-auto px-6">
  <hr style="border:none;border-top:1px solid rgba(239,168,38,0.2)"/>
</div>


<script>
  function stepPassenger(delta){
    var inp = document.getElementById('passCount');
    var v = parseInt(inp.value) + delta;
    inp.value = Math.max(1, Math.min(8, v));
  }
  document.querySelector('select[name="from"]')?.addEventListener('change', function(){
    var toSel = document.querySelector('select[name="to"]');
    Array.from(toSel.options).forEach(function(o){ o.disabled = o.value && o.value === this.value; }.bind(this));
  });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
