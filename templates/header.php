<?php

require_once __DIR__ . '/../inc/functions.php';

$pageTitle = $pageTitle ?? 'IARADIA';

$user = current_user();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= sanitize($pageTitle) ?> — IARADIA</title>
  <meta name="description" content="IARADIA — Transport premium à Madagascar. Réservez votre voyage en ligne."/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            gold:  '#D4A574',
            ivory: '#1a1a1a',
            sage:  '#5C8B5A',
            terra: '#C96A3A',
            navy:  '#F5F1EB',
          },
          fontFamily: {
            serif: ['Cormorant Garamond','Georgia','serif'],
            sans:  ['Inter','system-ui','sans-serif'],
          },
        }
      }
    }
    
  </script>
  <link rel="stylesheet" href="/assets/css/app.css"/>
</head>
<body class="bg-navy text-ivory min-h-screen">
<?php include __DIR__ . '/nav.php'; ?>
<main class="pt-16">