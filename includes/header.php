<?php
$tituloPagina = $tituloPagina ?? 'Painel';
$cssPagina = $cssPagina ?? 'index.css';
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?> | HelpDesk Fácil</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/<?= htmlspecialchars($cssPagina, ENT_QUOTES, 'UTF-8') ?>">
</head>

<body>
  <header class="topbar">
    <a class="brand" href="index.php"><span class="brand-icon">H</span> HelpDesk <strong>Fácil</strong></a>
    <nav>
      <a href="index.php">Painel</a>
      <a href="chamados.php">Chamados</a>
      <?php if (isset($_SESSION['nome'])): ?>
        <span class="nav-usuario">Olá, <?= e($_SESSION['nome']) ?><?= isAdmin() ? ' (Administrador)' : '' ?></span>
        <a class="nav-button" href="logout.php">Sair</a>
      <?php endif; ?>
    </nav>
  </header>
  <main class="container">
<<<<<<< HEAD
  <?php if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="flash flash-<?= e($flash['tipo']) ?>" role="alert"><?= e($flash['texto']) ?></div>
  <?php endif; ?>
=======
    <?php if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash'];
      unset($_SESSION['flash']); ?>
      <div class="flash flash-<?= e($flash['tipo']) ?>" role="alert"><?= e($flash['texto']) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash'];
      unset($_SESSION['flash']); ?>
      <div class="flash flash-<?= e($flash['tipo']) ?>" role="alert"><?= e($flash['texto']) ?></div>
    <?php endif; ?>
>>>>>>> 88014356061da972a4a6f2740bb8590f5ad8a826
