<?php

require_once __DIR__ . '/inc/functions.php';

$pageTitle = 'Destinations';

$allVoyages = getVoyages();

include __DIR__ . '/templates/header.php';
?>

<div class="overflow-x-auto my-5">
    <table class="min-w-full border-collapse" style="background: rgba(245,241,235,0.02); color: var(--color-ivory);">
        <thead>
            <tr style="background: rgba(245,241,235,0.05);">
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">ID</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Départ</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Véhicule</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Distance</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Origine</th>
                <th class="px-4 py-2 border border-white/10 text-left text-xs font-bold uppercase tracking-wider">Destination</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allVoyages as $v): ?>
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-4 py-2 border border-white/10 text-sm"><?= $v['id_Voyage'] ?></td>
                <td class="px-4 py-2 border border-white/10 text-sm"><?= date('d/m/Y H:i', strtotime($v['date_depart_Voyage'])) ?></td>
                <td class="px-4 py-2 border border-white/10 text-sm">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" 
                          style="background: <?= $v['status_Voyage'] === 'planifie' ? '#1DA06E20' : '#EFA82620' ?>; 
                                 color: <?= $v['status_Voyage'] === 'planifie' ? '#1DA06E' : '#EFA826' ?>;">
                        <?= $v['status_Voyage'] ?>
                    </span>
                </td>
                <?php
                  $intermidiateTrajet = getIntermidiateTrajet($v['id_Trajet']);
                  
                  ?>
                  <table>
                  <?php foreach($intermidiateTrajet as $inter): ?>
                    <tr><td> <?= inter['nom'] ?> </td></tr>
                    <?php endforeach;?>
                  </table>

                <td class="px-4 py-2 border border-white/10 text-sm font-mono"><?= $v['immatriculation_Vehicule'] ?></td>
                <td class="px-4 py-2 border border-white/10 text-sm"><?= number_format($v['distance_km_Trajet'], 0) ?> km</td>
                <td class="px-4 py-2 border border-white/10 text-sm"><?= htmlspecialchars($v['from_city']) ?></td>
                <td class="px-4 py-2 border border-white/10 text-sm"><?= htmlspecialchars($v['to_city']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>