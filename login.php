<?php
require_once __DIR__ . '/inc/functions.php';
$pageTitle = 'Connexion';

// Redirect if already logged in
if (is_logged_in()) {
    $u = current_user();
    $role = strtoupper($u['role'] ?? '');
    redirect(($role === 'AGENT' || $role === 'ADMIN') ? '/dashboard-agent.php' : '/index.php');
}

$error   = flash_get('auth_error') ?? '';
$success = flash_get('auth_success') ?? '';

// logique de connexion
if (is_post()) {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
        $email = sanitize(trim($_POST['email'] ?? ''));
        $pass  = $_POST['password'] ?? '';
        if (!$email || !$pass) {
            flash_set('auth_error', 'Veuillez remplir tous les champs.');
            redirect('/login.php');
        }
        $u = authenticate_user($email, $pass);
        if ($u) {
            login_user($u);
            $role = strtoupper($u['role']);
            redirect(($role === 'AGENT' || $role === 'ADMIN') ? '/dashboard-agent.php' : '/index.php');
        } else {
            flash_set('auth_error', 'Email ou mot de passe incorrect.');
            redirect('/login.php');
        }

    } elseif ($action === 'register') {
        $name  = sanitize(trim($_POST['name']     ?? ''));
        $email = sanitize(trim($_POST['email']    ?? ''));
        $pass  = $_POST['password']  ?? '';

        $role  = 'client'; // default

        if (!$name || !$email || !$pass) {
            flash_set('auth_error', 'Tous les champs sont requis.');
            redirect('/login.php?tab=register');
        }
        $u = register_user($name, $email, $pass, $role);
        if ($u) {
            login_user($u);
            redirect(strtoupper($u['role']) === 'AGENT' ? '/dashboard-agent.php' : '/index.php');
        } else {
            flash_set('auth_error', 'Cet email est déjà utilisé.');
            redirect('/login.php?tab=register');
        }
    }
}

$activeTab = $_GET['tab'] ?? 'login';
include __DIR__ . '/templates/header.php';
?>

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2" style="background:var(--color-navy)">
  <div class="hidden lg:block relative bg-cover bg-center"
       style="background-image:url('<?= cityImg("toliara") ?>')" >
    <div class="absolute inset-0" ></div>
    <div class="absolute inset-0" ></div>
    <div class="absolute bottom-12 left-12 max-w-sm">
      <p class="font-serif text-5xl text-white font-bold leading-tight mb-4">
        Voyagez avec<br><span style="color:var(--color-gold)" class="italic">confiance</span>
      </p>
    </div>
    <div class="absolute top-8 left-8">
      <span class="font-serif text-2xl text-white">Ia<span style="color:var(--color-gold)">radia</span></span>
    </div>
  </div>

  <div class="flex items-center justify-center px-6 py-16">
    <div class="w-full max-w-sm">
      <div class="lg:hidden text-center mb-10">
        <span class="font-serif text-3xl text-ivory">Ia<span class="text-gold">radia</span></span>
      </div>

      <?php if ($error): ?>
        <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-50 border border-red-200 text-red-700">
          <?= sanitize($error) ?>
        </div>
      <?php endif; ?>

      <div class="flex mb-8">
        <?php foreach (['login'=>'Connexion','register'=>'Inscription'] as $tab => $label): ?>
          <button type="button" id="tab-btn-<?= $tab ?>"
                  onclick="switchTab('<?= $tab ?>')"
                  class="flex-1 pb-3 text-sm font-medium transition <?= $activeTab === $tab ? 'text-ivory' : 'text-ivory/40' ?>">
            <?= $label ?>
          </button>
        <?php endforeach; ?>
      </div>

      <form id="form-login" method="POST" action="/login.php"
            class="<?= $activeTab !== 'login' ? 'hidden' : '' ?>">
        <input type="hidden" name="action" value="login"/>
        <div class="space-y-4">
          <label class="block text-xs uppercase tracking-wider text-white/40">Email</label>
          <input type="email" name="email" required placeholder="votre@email.mg"
                class="w-full rounded-xl px-4 py-3 text-sm bg-white/5 border border-white/10 text-ivory focus:outline-none"/>

          <label class="block text-xs uppercase tracking-wider text-white/40">Mot de passe</label>
          <input type="password" name="password" required placeholder="••••••••"
                class="w-full rounded-xl px-4 py-3 text-sm bg-white/5 border border-white/10 text-ivory focus:outline-none"/>

          <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium bg-gold text-slate-950">
            Se connecter
          </button>
        </div>
      </form>

      <form id="form-register" method="POST" action="/login.php"
            class="<?= $activeTab !== 'register' ? 'hidden' : '' ?>">
        <input type="hidden" name="action" value="register"/>
        <div class="space-y-4">
          <label class="block text-xs uppercase tracking-wider text-white/40">Nom complet</label>
          <input type="text" name="name" required placeholder="Rakoto Andry"
                class="w-full rounded-xl px-4 py-3 text-sm bg-white/5 border border-white/10 text-ivory focus:outline-none"/>

          <label class="block text-xs uppercase tracking-wider text-white/40">Email</label>
          <input type="email" name="email" required placeholder="votre@email.mg"
                class="w-full rounded-xl px-4 py-3 text-sm bg-white/5 border border-white/10 text-ivory focus:outline-none"/>

          <label class="block text-xs uppercase tracking-wider text-white/40">Mot de passe</label>
          <input type="password" name="password" required placeholder="••••••••"
                class="w-full rounded-xl px-4 py-3 text-sm bg-white/5 border border-white/10 text-ivory focus:outline-none"/>

          <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium bg-gold text-slate-950">
            S'inscrire
          </button>
        </div>
      </form>
    </div>
</div>

<script>
function switchTab(tab) {
  document.getElementById('form-login').classList.toggle('hidden', tab !== 'login');
  document.getElementById('form-register').classList.toggle('hidden', tab !== 'register');
  ['login','register'].forEach(function(t) {
    var btn = document.getElementById('tab-btn-' + t);
    btn.classList.toggle('text-ivory', t === tab);
    btn.classList.toggle('text-ivory/40', t !== tab);
  });
}
</script>


</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
