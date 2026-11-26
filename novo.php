<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirLogin();

$usuarios = $pdo->query('SELECT id_usuario, nome FROM usuarios ORDER BY nome')->fetchAll();
$categorias = $pdo->query('SELECT id_categoria, nome FROM categorias ORDER BY nome')->fetchAll();
$erros = [];
$valores = ['titulo' => '', 'descricao' => '', 'solicitante' => '', 'id_categoria' => '', 'prioridade' => 'Media'];
if (!isAdmin()) $valores['solicitante'] = $_SESSION['nome'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($valores as $campo => $padrao) $valores[$campo] = trim($_POST[$campo] ?? $padrao);
  if (!isAdmin()) $valores['solicitante'] = $_SESSION['nome'];
  if ($valores['titulo'] === '') $erros[] = 'Informe o título do chamado.';
  if ($valores['descricao'] === '') $erros[] = 'Informe a descrição do problema.';
  if ($valores['solicitante'] === '') $erros[] = 'Informe o nome do solicitante.';
  elseif (mb_strlen($valores['solicitante']) > 100) $erros[] = 'O nome do solicitante deve ter no máximo 100 caracteres.';
  if (!filter_var($valores['id_categoria'], FILTER_VALIDATE_INT)) $erros[] = 'Selecione uma categoria válida.';
  if (!in_array($valores['prioridade'], ['Alta', 'Media', 'Baixa'], true)) $erros[] = 'Selecione uma prioridade válida.';
  $validCategoria = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id_categoria = ?');
  $validCategoria->execute([$valores['id_categoria']]);
  if ((int)$validCategoria->fetchColumn() === 0) $erros[] = 'A categoria selecionada não existe.';
  if (!$erros) {
    $idUsuario = isAdmin() ? obterOuCriarUsuario($pdo, $valores['solicitante']) : (int)$_SESSION['id_usuario'];
    $stmt = $pdo->prepare('INSERT INTO chamados (titulo, descricao, prioridade, id_usuario, id_categoria, id_criador) VALUES (:titulo, :descricao, :prioridade, :id_usuario, :id_categoria, :id_criador)');
    $stmt->execute(['titulo' => $valores['titulo'], 'descricao' => $valores['descricao'], 'prioridade' => $valores['prioridade'], 'id_usuario' => $idUsuario, 'id_categoria' => (int)$valores['id_categoria'], 'id_criador' => (int)$_SESSION['id_usuario']]);
    redirecionar('chamados.php');
  }
}
$cssPagina = 'novo.css';
$tituloPagina = 'Novo chamado';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
  <div>
    <p class="eyebrow">NOVA SOLICITAÇÃO</p>
    <h1>Abrir chamado</h1>
    <p class="muted">Preencha os dados do problema técnico.</p>
  </div><a class="button button-secondary" href="chamados.php">Voltar</a>
</section>
<section class="panel form-panel">
  <?php if (!$categorias): ?><div class="alert">
      <p>Cadastre pelo menos uma categoria no banco de dados antes de abrir um chamado.</p>
    </div><?php endif; ?>
  <?php if ($erros): ?><div class="alert"><?php foreach ($erros as $erro): ?><p><?= e($erro) ?></p><?php endforeach; ?></div><?php endif; ?>
  <form method="post" class="ticket-form">
    <label>Título do problema<input name="titulo" maxlength="100" required value="<?= e($valores['titulo']) ?>" placeholder="Ex.: Computador não liga"></label>
    <?php if (isAdmin()): ?>
    <label>Solicitante<input name="solicitante" list="lista-solicitantes" maxlength="100" required autocomplete="off" value="<?= e($valores['solicitante']) ?>" placeholder="Digite o nome do solicitante"></label>
    <datalist id="lista-solicitantes"><?php foreach ($usuarios as $u): ?><option value="<?= e($u['nome']) ?>"></option><?php endforeach; ?></datalist>
    <?php else: ?>
    <label>Solicitante<input value="<?= e($_SESSION['nome']) ?>" readonly aria-readonly="true" style="background:#f2f4f7;cursor:not-allowed">
      <small style="color:#667085;font-weight:400">O chamado será aberto no seu nome.</small></label>
    <?php endif; ?>
    <div class="form-row">
      <label>Categoria<select name="id_categoria" required>
          <option value="">Selecione</option><?php foreach ($categorias as $cat): ?><option value="<?= (int)$cat['id_categoria'] ?>" <?= (string)$valores['id_categoria'] === (string)$cat['id_categoria'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?>
        </select></label>
      <label>Prioridade<select name="prioridade"><?php foreach (['Alta', 'Media', 'Baixa'] as $op): ?><option value="<?= e($op) ?>" <?= $valores['prioridade'] === $op ? 'selected' : '' ?>><?= e($op) ?></option><?php endforeach; ?></select></label>
    </div>
    <label>Descrição<textarea name="descricao" rows="5" required placeholder="Descreva o que está acontecendo..."><?= e($valores['descricao']) ?></textarea></label>
    <div class="form-actions"><a class="text-link" href="chamados.php">Cancelar</a><button class="button" type="submit" <?= !$categorias ? 'disabled' : '' ?>>Salvar chamado</button></div>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>