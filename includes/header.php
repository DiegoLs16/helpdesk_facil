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
    </nav>
  </header>
  <main class="container">