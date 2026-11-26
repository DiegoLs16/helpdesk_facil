<?php
session_start();
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$erro = '';
$emailDigitado = '';

if (isset($_SESSION['id_usuario'])) {
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $emailDigitado = trim($_POST['email'] ?? '');
  if (login($pdo, $emailDigitado, (string) ($_POST['senha'] ?? ''))) {
    header("Location: index.php");
    exit;
  } else {
    $erro = "Email ou senha incorretos";
  }
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | HelpDesk Fácil</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/login.css">
</head>

<body class="login-page">
  <main class="login-container">
    <div class="login-brand-wrapper">
      <a class="brand" href="index.php">
        <span class="brand-icon">H</span> HelpDesk <strong>Fácil</strong>
      </a>
    </div>

    <section class="login-card">
      <header class="login-header">
        <h1>Acesse sua conta</h1>
        <p>Informe suas credenciais para gerenciar chamados</p>
      </header>

      <?php if ($erro): ?>
        <div class="login-erro" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form class="login-form" action="login.php" method="POST" autocomplete="on">
        <div class="form-group <?php echo $erro ? 'error' : ''; ?>">
          <label for="email">E-mail</label>
          <div class="input-wrapper">
            <span class="input-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
            </span>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="exemplo@email.com"
              value="<?= htmlspecialchars($emailDigitado, ENT_QUOTES, 'UTF-8') ?>"
              required
              autofocus>
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
            <input
              type="password"
              id="senha"
              name="senha"
              placeholder="Digite sua senha"
              required>
            <button
              type="button"
              id="toggleSenha"
              class="password-toggle"
              aria-label="Mostrar ou ocultar senha"
              title="Mostrar senha">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="button btn-login">
          Entrar no sistema
        </button>

        <div class="login-footer-info">
          Ainda não tem conta? <a href="cadastro.php">Criar conta</a>
        </div>
      </form>
    </section>

    <footer class="login-page-footer">
      HelpDesk Fácil &middot; Sistema de Suporte Técnico
    </footer>
  </main>

  <script>
    const toggleBtn = document.getElementById('toggleSenha');
    const senhaInput = document.getElementById('senha');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && senhaInput) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = senhaInput.getAttribute('type') === 'password';
        senhaInput.setAttribute('type', isPassword ? 'text' : 'password');
        toggleBtn.setAttribute('title', isPassword ? 'Ocultar senha' : 'Mostrar senha');

        if (isPassword) {
          eyeIcon.innerHTML = `
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
            <line x1="2" y1="2" x2="22" y2="22"></line>
          `;
        } else {
          eyeIcon.innerHTML = `
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          `;
        }
      });
    }
  </script>
</body>

</html>