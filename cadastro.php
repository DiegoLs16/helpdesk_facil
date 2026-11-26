<?php
session_start();
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

if (isset($_SESSION['id_usuario'])) {
  header('Location: index.php');
  exit;
}

$erros = [];
$valores = ['nome' => '', 'email' => '', 'telefone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $valores['nome'] = trim(preg_replace('/\s+/', ' ', $_POST['nome'] ?? ''));
  $valores['email'] = strtolower(trim($_POST['email'] ?? ''));
  $valores['telefone'] = trim($_POST['telefone'] ?? '');
  $senha = (string) ($_POST['senha'] ?? '');
  $confirmar = (string) ($_POST['confirmar'] ?? '');

  if (mb_strlen($valores['nome']) < 2) {
    $erros[] = 'Informe seu nome (mínimo de 2 letras).';
  } elseif (mb_strlen($valores['nome']) > 100) {
    $erros[] = 'O nome deve ter no máximo 100 caracteres.';
  }
  if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($valores['email']) > 100) {
    $erros[] = 'Informe um e-mail válido.';
  }
  if ($valores['telefone'] !== '' && !preg_match('/^[0-9()+\-\s]{8,20}$/', $valores['telefone'])) {
    $erros[] = 'Telefone inválido. Use apenas números, espaços, parênteses, + e -.';
  }
  if (mb_strlen($senha) < 8) {
    $erros[] = 'A senha precisa ter pelo menos 8 caracteres.';
  } elseif (mb_strlen($senha) > 72) {
    $erros[] = 'A senha deve ter no máximo 72 caracteres.';
  }
  if ($senha !== $confirmar) {
    $erros[] = 'A confirmação da senha não confere.';
  }

  $idReaproveitar = null;
  if (!$erros) {
    $stmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$valores['email']]);
    if ($stmt->fetchColumn()) {
      $erros[] = 'Já existe uma conta com esse e-mail. Tente entrar.';
    }

    $stmt = $pdo->prepare('SELECT id_usuario, email FROM usuarios WHERE nome = ?');
    $stmt->execute([$valores['nome']]);
    foreach ($stmt->fetchAll() as $existente) {
      if ($existente['email'] !== '') {
        $erros[] = 'Já existe uma conta com esse nome. Use outro nome (por exemplo, com sobrenome).';
        break;
      }
      if ($idReaproveitar === null) {
        $idReaproveitar = (int) $existente['id_usuario'];
      }
    }
  }

  if (!$erros) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $telefone = $valores['telefone'] !== '' ? $valores['telefone'] : null;

    if ($idReaproveitar !== null) {
      $pdo->prepare("UPDATE usuarios SET email = ?, senha = ?, telefone = ?, permissao = 'Usuario' WHERE id_usuario = ? AND email = ''")
        ->execute([$valores['email'], $hash, $telefone, $idReaproveitar]);
    } else {
      $pdo->prepare("INSERT INTO usuarios (nome, email, senha, permissao, telefone) VALUES (?, ?, ?, 'Usuario', ?)")
        ->execute([$valores['nome'], $valores['email'], $hash, $telefone]);
    }

    if (login($pdo, $valores['email'], $senha)) {
      definirFlash('ok', 'Conta criada com sucesso. Bem-vindo(a), ' . $valores['nome'] . '!');
      header('Location: index.php');
      exit;
    }
    header('Location: login.php');
    exit;
  }
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar conta | HelpDesk Fácil</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/login.css">
</head>

<body class="login-page">
  <main class="login-container">
    <div class="login-brand-wrapper">
      <a class="brand" href="login.php">
        <span class="brand-icon">H</span> HelpDesk <strong>Fácil</strong>
      </a>
    </div>

    <section class="login-card">
      <header class="login-header">
        <h1>Criar conta</h1>
        <p>Cadastre-se para abrir e acompanhar seus chamados</p>
      </header>

      <?php if ($erros): ?>
        <div class="login-erro" role="alert">
          <?php foreach ($erros as $erro): ?>
            <?= e($erro) ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="login-form" action="cadastro.php" method="POST" autocomplete="on">
        <div class="form-group">
          <label for="nome">Nome</label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </span>
            <input type="text" id="nome" name="nome" maxlength="100" placeholder="Seu nome completo"
              autocomplete="name" required autofocus value="<?= e($valores['nome']) ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="email">E-mail</label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
            </span>
            <input type="email" id="email" name="email" maxlength="100" placeholder="exemplo@email.com"
              autocomplete="email" required value="<?= e($valores['email']) ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="telefone">Telefone <span style="font-weight:400;color:#98a2b3">(opcional)</span></label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
            </span>
            <input type="tel" id="telefone" name="telefone" maxlength="20" placeholder="(48) 99999-0000"
              autocomplete="tel" value="<?= e($valores['telefone']) ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="senha">Senha</label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input type="password" id="senha" name="senha" minlength="8" maxlength="72"
              placeholder="Mínimo de 8 caracteres" autocomplete="new-password" required>
          </div>
        </div>

        <div class="form-group">
          <label for="confirmar">Confirmar senha</label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input type="password" id="confirmar" name="confirmar" minlength="8" maxlength="72"
              placeholder="Repita a senha" autocomplete="new-password" required>
          </div>
        </div>

        <button type="submit" class="button btn-login">Criar conta</button>

        <div class="login-footer-info">
          Já tem uma conta? <a href="login.php">Entrar</a>
        </div>
      </form>
    </section>

    <footer class="login-page-footer">
      HelpDesk Fácil &middot; Sistema de Suporte Técnico
    </footer>
  </main>
</body>

</html>