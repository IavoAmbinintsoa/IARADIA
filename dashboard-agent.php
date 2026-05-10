<?php
require_once __DIR__ . '/inc/functions.php'; // utility

$user = current_user();

if (!$user){
   redirect('/login.php');
}

$role = strtoupper($user['role']);
if (!in_array($role, ['AGENT','ADMIN'])){
   redirect('/index.php');
}

$pageTitle = 'Tableau de bord';

$allVoyages = getVoyages();

$today        = date('Y-m-d');
$todayVoyages = array_filter($allVoyages, fn($v) => str_starts_with($v['date_depart_Voyage'], $today));
$totalSeats   = array_sum(array_column($allVoyages, 'vendus') ?: [0]);
$totalVoyages = count($allVoyages);

$vehicules = getVehicule();

$statuses = ['Tous','planifie','en_cours','termine','annule'];

include __DIR__ . '/templates/header.php';
?>

<style>
  .txt-ivory { color: var(--color-ivory); }
  .txt-gold { color: var(--color-gold); }
  .txt-dim { color: rgba(212, 165, 116, 0.4); }
  .row-hover:hover { background: rgba(212,165,116,0.04); }
  .tab-btn { transition: all .2s; }
  .btn-hover:hover { opacity: 0.7; }
</style>

<div class="min-h-screen py-16" style="background:var(--color-navy)">
  <div class="max-w-4xl mx-auto px-6">

    <div class="relative mb-20">
      <div class="h-52 w-full" style="background:linear-gradient(135deg,rgba(212,165,116,0.25) 0%,rgba(212,165,116,0.05) 100%)"></div>
      <div class="max-w-3xl mx-auto px-6 relative">
        <div class="absolute -top-14 left-6 flex items-end gap-5">
          <div class="w-28 h-28 rounded-full flex items-center justify-center text-4xl font-serif font-bold flex-shrink-0 txt-gold"
               style="background:rgba(212,165,116,0.15); border:3px solid var(--color-navy); box-shadow:0 0 0 2px rgba(212,165,116,0.3)">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
          </div>
          <div class="pb-2">
            <h1 class="text-2xl font-serif font-bold leading-tight txt-ivory"><?= sanitize($user['name']) ?></h1>
            <p class="text-sm mt-0.5 txt-dim"><?= sanitize($user['email']) ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-8 mb-12" style="border-bottom:1px solid rgba(212,165,116,0.15)">
      <?php foreach (['voyages'=>'Voyages','vehicules'=>'Flotte'] as $tid=>$tlbl): ?>
        <button type="button" onclick="switchTab('<?= $tid ?>')" id="tab-<?= $tid ?>"
                class="tab-btn pb-4 text-sm font-medium uppercase tracking-widest txt-ivory"
                style="border-bottom:2px solid transparent; margin-bottom:-1px">
          <?= $tlbl ?>
        </button>
      <?php endforeach; ?>
    </div>

    <div id="panel-voyages">
      <div class="flex flex-wrap gap-6 mb-10">
        <?php foreach ($statuses as $s): ?>
          <button type="button" onclick="filterStatus('<?= $s ?>')" data-status="<?= $s ?>"
                  class="status-btn text-xs uppercase tracking-widest transition-colors txt-dim">
            <?= $s === 'Tous' ? 'Tous' : ucfirst(str_replace('_',' ',$s)) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="grid grid-cols-12 gap-4 pb-3 mb-1 txt-dim" style="border-bottom:1px solid rgba(212,165,116,0.08)">
        <span class="col-span-1 text-xs uppercase tracking-widest">ID</span>
        <span class="col-span-4 text-xs uppercase tracking-widest">Trajet</span>
        <span class="col-span-3 text-xs uppercase tracking-widest">Départ</span>
        <span class="col-span-2 text-xs uppercase tracking-widest">Immat.</span>
        <span class="col-span-2 text-xs uppercase tracking-widest">Statut</span>
      </div>

      <?php if ($allVoyages): ?>
        <?php foreach ($allVoyages as $v): ?>
          <?php
            $heure = date('H:i', strtotime($v['date_depart_Voyage']));
            $dateD = date('d/m/Y', strtotime($v['date_depart_Voyage']));
            $stColor = match($v['status_Voyage']) {
              'planifie' => 'rgba(212,165,116,0.6)',
              'en_cours' => 'var(--color-ivory)',
              'termine'  => 'rgba(212,165,116,0.25)',
              'annule'   => 'rgba(226,75,74,0.6)',
              default    => 'rgba(212,165,116,0.3)',
            };
          ?>
          <div class="voyage-row row-hover grid grid-cols-12 gap-4 py-4 cursor-default" data-status="<?= $v['status_Voyage'] ?>" style="border-bottom:1px solid rgba(212,165,116,0.05)">
            <span class="col-span-1 text-xs font-mono self-center txt-dim"><?= $v['id_Voyage'] ?></span>
            <span class="col-span-4 text-sm font-medium self-center txt-ivory"><?= sanitize($v['from_city']) ?> → <?= sanitize($v['to_city']) ?></span>
            <span class="col-span-3 text-xs self-center txt-ivory"><?= $heure ?> · <?= $dateD ?></span>
            <span class="col-span-2 text-xs font-mono self-center txt-ivory"><?= sanitize($v['immatriculation_Vehicule'] ?? '—') ?></span>
            <div class="col-span-2 flex items-center justify-between">
              <span class="text-xs uppercase tracking-widest font-bold" style="color:<?= $stColor ?>"><?= strtoupper(str_replace('_',' ',$v['status_Voyage'])) ?></span>
              <?php if ($v['status_Voyage'] === 'planifie'): ?>
                <form method="POST" action="/api/voyages.php">
                  <input type="hidden" name="action" value="cancel"/><input type="hidden" name="id" value="<?= $v['id_Voyage'] ?>"/>
                  <button type="submit" class="text-[10px] uppercase tracking-widest px-2 py-1 rounded" style="background:rgba(226,75,74,0.1); color:rgba(226,75,74,0.8)">Annuler</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <button onclick="document.getElementById('new-voyage-modal').style.display='flex'" class="mt-12 text-sm font-bold uppercase tracking-widest txt-gold btn-hover">+ Nouveau voyage</button>
    </div>

    <div id="panel-vehicules" class="hidden">
      <div class="grid grid-cols-12 gap-4 pb-3 mb-1 txt-dim" style="border-bottom:1px solid rgba(212,165,116,0.08)">
        <span class="col-span-3 text-xs uppercase tracking-widest">Immat.</span>
        <span class="col-span-4 text-xs uppercase tracking-widest">Modèle</span>
        <span class="col-span-2 text-xs uppercase tracking-widest">Places</span>
        <span class="col-span-3 text-xs uppercase tracking-widest">Chauffeur</span>
      </div>
      <?php if ($vehicules): foreach ($vehicules as $v): ?>
        <div class="row-hover grid grid-cols-12 gap-4 py-4 txt-ivory" style="border-bottom:1px solid rgba(212,165,116,0.05)">
          <span class="col-span-3 text-sm font-mono font-bold self-center"><?= sanitize($v['plate']) ?></span>
          <span class="col-span-4 text-sm self-center"><?= sanitize($v['model'] ?? '—') ?></span>
          <span class="col-span-2 text-sm self-center"><?= $v['seats'] ?? '—' ?></span>
          <div class="col-span-3 flex items-center justify-between">
            <span class="text-sm self-center txt-dim"><?= sanitize($v['chauffeur'] ?? '—') ?></span>
            <button class="text-[10px] uppercase tracking-widest px-2 py-1 rounded txt-gold" style="border:1px solid rgba(212,165,116,0.3)">Assigner</button>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<div id="new-voyage-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-6" style="background:rgba(10,14,26,0.9); backdrop-filter:blur(6px)" onclick="this.style.display='none'">
  <div class="max-w-md w-full p-10 rounded-2xl" style="background:var(--color-navy); border:1px solid rgba(212,165,116,0.2)" onclick="event.stopPropagation()">
    <h3 class="font-serif text-2xl font-bold mb-8 txt-ivory">Créer un voyage</h3>
    <form method="POST" action="/api/voyages.php" class="space-y-6">
      <input type="hidden" name="action" value="create"/>
      <?php foreach ([['from','Départ'],['to','Arrivée']] as [$n,$l]): ?>
      <div>
        <label class="text-[10px] uppercase tracking-widest txt-dim block mb-1"><?= $l ?></label>
        <select name="<?= $n ?>" required class="w-full py-2 bg-transparent txt-ivory focus:outline-none" style="border-bottom:1px solid rgba(212,165,116,0.2)">
          <option value="" style="background:var(--color-navy)">Choisir...</option>
          <?php foreach (getCities() as $c): ?><option value="<?= sanitize($c) ?>" style="background:var(--color-navy)"><?= sanitize($c) ?></option><?php endforeach; ?>
        </select>
      </div>
      <?php endforeach; ?>
      <div>
        <label class="text-[10px] uppercase tracking-widest txt-dim block mb-1">Date & Heure</label>
        <input type="datetime-local" name="departure" required class="w-full py-2 bg-transparent txt-ivory focus:outline-none" style="border-bottom:1px solid rgba(212,165,116,0.2)"/>
      </div>
      <div class="flex gap-6 pt-4">
        <button type="button" onclick="document.getElementById('new-voyage-modal').style.display='none'" class="text-xs uppercase txt-dim">Annuler</button>
        <button type="submit" class="text-xs font-bold uppercase txt-gold">Créer →</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchTab(t) {
  ['voyages','vehicules'].forEach(function(id) {
    var panel = document.getElementById('panel-'+id);
    var btn   = document.getElementById('tab-'+id);
    var active = id === t;
    panel.classList.toggle('hidden', !active);
    btn.style.color = active ? 'var(--color-gold)' : 'rgba(212,165,116,0.4)';
    btn.style.borderColor = active ? 'var(--color-gold)' : 'transparent';
  });
}

function filterStatus(s) {
  document.querySelectorAll('.status-btn').forEach(function(btn) {
    btn.style.color = btn.dataset.status === s ? 'var(--color-gold)' : 'rgba(212,165,116,0.4)';
  });
  document.querySelectorAll('.voyage-row').forEach(function(row) {
    row.style.display = (s === 'Tous' || row.dataset.status === s) ? 'grid' : 'none';
  });
}
switchTab('voyages');
filterStatus('Tous');
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
