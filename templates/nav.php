<?php

$user = current_user();
$role = strtoupper($user['role'] ?? '');
$currentFile = basename($_SERVER['PHP_SELF']);

if ($role === 'AGENT' || $role === 'ADMIN') {
    $navLinks = [
        ['href' => '/dashboard-agent.php', 'label' => 'Tableau de bord'],
        ['href' => '/destinations.php',     'label' => 'Destinations'],
    ];
} else {
    $navLinks = [
        ['href' => '/index.php',      'label' => 'Accueil'],
        ['href' => '/destinations.php','label' => 'Destinations'],
        ['href' => '/search.php',      'label' => 'Voyages'],
    ];
    if ($user) {
        $navLinks[] = ['href' => '/dashboard-client.php', 'label' => 'Mon compte'];
    }
}

function navLinkClass(string $href, string $current): string {
    $active = (basename($href) === $current || ($href === '/index.php' && $current === 'index.php'));
    return $active
        ? 'px-4 py-1.5 rounded-full text-sm font-medium nav-link-active transition-all duration-300'
        : 'px-4 py-1.5 rounded-full text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-black/5 transition-all duration-300';
}
?>
<nav class="fixed top-0 left-0 right-0 z-50 glass-dark" id="main-nav">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

    <!-- Logo -->
    <a href="/index.php" class="flex items-center gap-2 group">
      <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-all group-hover:glow-gold"
            style="background:var(--color-gold);color:#F5F1EB">I</span>
      <span class="font-serif text-xl tracking-wide" style="color:var(--color-ivory)">
        Ia<span style="color:var(--color-gold)">radia</span>
      </span>
    </a>

    <div class="hidden md:flex items-center gap-1">
      <?php foreach ($navLinks as $l): ?>
        <a href="<?= $l['href'] ?>" class="<?= navLinkClass($l['href'], $currentFile) ?>">
          <?= sanitize($l['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="flex items-center gap-3">
      <?php if ($user): ?>
        <span class="hidden md:block text-sm" style="color:rgba(26,26,26,0.5)"><?= sanitize($user['name']) ?></span>
        <?php
          $roleStyle = match($role) {
              'AGENT' => 'color:var(--color-gold);border-color:rgba(212,165,116,0.3);background:rgba(212,165,116,0.1)',
              'ADMIN' => 'color:var(--color-terra);border-color:rgba(201,106,58,0.3);background:rgba(201,106,58,0.1)',
              default => 'color:var(--color-sage);border-color:rgba(92,139,90,0.3);background:rgba(92,139,90,0.1)',
          };
        ?>
        <span class="text-xs px-2 py-0.5 rounded-full border font-medium" style="<?= $roleStyle ?>"><?= $role ?></span>
        <a href="/logout.php" class="text-xs transition-colors" style="color:rgba(26,26,26,0.4)" onmouseover="this.style.color='var(--color-ivory)'" onmouseout="this.style.color='rgba(26,26,26,0.4)'">Déconnexion</a>
      <?php else: ?>
        <a href="/login.php" class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-300 glow-gold"
           style="background:var(--color-gold);color:#F5F1EB">Connexion</a>
      <?php endif; ?>

      <!-- Hamburger -->
      <button class="md:hidden text-gray-700" id="nav-toggle" aria-label="Menu">
        <svg id="nav-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg id="nav-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="nav-mobile" class="hidden md:hidden glass-dark border-t border-black/5 px-6 py-4 flex flex-col gap-2">
    <?php foreach ($navLinks as $l): ?>
      <a href="<?= $l['href'] ?>" class="<?= navLinkClass($l['href'], $currentFile) ?>">
        <?= sanitize($l['label']) ?>
      </a>
    <?php endforeach; ?>
    <?php if ($user): ?>
      <a href="/logout.php" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-500">Déconnexion</a>
    <?php else: ?>
      <a href="/login.php" class="px-4 py-2 rounded-xl text-sm font-medium nav-link-active">Connexion</a>
    <?php endif; ?>
  </div>
</nav>

<script>
  (function(){
    var btn = document.getElementById('nav-toggle');
    var mob = document.getElementById('nav-mobile');
    var ico = document.getElementById('nav-icon-open');
    var icx = document.getElementById('nav-icon-close');
    if(btn) btn.addEventListener('click', function(){
      var open = mob.classList.toggle('hidden');
      ico.classList.toggle('hidden', !open);
      icx.classList.toggle('hidden', open);
    });
  })();
</script>