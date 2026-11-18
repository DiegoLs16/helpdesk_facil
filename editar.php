<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirecionar('chamados.php');
$stmt = $pdo->prepare('SELECT * FROM chamados WHERE id_chamado = ?');
$stmt->execute([$id]);
$chamado = $stmt->fetch();
if (!$chamado) redirecionar('chamados.php');
$usuarios = $pdo->query('SELECT id_usuario, nome FROM usuarios ORDER BY nome')->fetchAll();
$categorias = $pdo->query('SELECT id_categoria, nome FROM categorias ORDER BY nome')->fetchAll();
$erros = [];
$nomeSolicitante = $pdo->prepare('SELECT nome FROM usuarios WHERE id_usuario = ?');
$nomeSolicitante->execute([$chamado['id_usuario']]);
$chamado['solicitante'] = (string)$nomeSolicitante->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach (['titulo', 'descricao', 'prioridade', 'status'] as $campo) $chamado[$campo] = trim($_POST[$campo] ?? '');
  $chamado['solicitante'] = trim($_POST['solicitante'] ?? '');
  $chamado['id_categoria'] = (int)($_POST['id_categoria'] ?? 0);
  if ($chamado['titulo'] === '' || $chamado['descricao'] === '') $erros[] = 'Preencha todos os campos obrigatórios.';
  if (!in_array($chamado['prioridade'], ['Alta', 'Media', 'Baixa'], true)) $erros[] = 'Prioridade inválida.';
  if (!in_array($chamado['status'], ['Aberto', 'Em andamento', 'Resolvido'], true)) $erros[] = 'Status inválido.';
  if ($chamado['solicitante'] === '') $erros[] = 'Informe o nome do solicitante.';
  elseif (mb_strlen($chamado['solicitante']) > 100) $erros[] = 'O nome do solicitante deve ter no máximo 100 caracteres.';
  $check = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id_categoria = ?');
  $check->execute([$chamado['id_categoria']]);
  if ((int)$check->fetchColumn() === 0) $erros[] = 'Selecione uma categoria válida.';
  if (!$erros) {
    $chamado['id_usuario'] = obterOuCriarUsuario($pdo, $chamado['solicitante']);
    $stmt = $pdo->prepare('UPDATE chamados SET titulo=:titulo, descricao=:descricao, prioridade=:prioridade, status=:status, id_usuario=:id_usuario, id_categoria=:id_categoria WHERE id_chamado=:id');
    $stmt->execute(['titulo' => $chamado['titulo'], 'descricao' => $chamado['descricao'], 'prioridade' => $chamado['prioridade'], 'status' => $chamado['status'], 'id_usuario' => $chamado['id_usuario'], 'id_categoria' => $chamado['id_categoria'], 'id' => $id]);
    redirecionar('chamados.php');
  }
}
$cssPagina = 'editar.css';
$tituloPagina = 'Editar chamado';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
  <div>
    <p class="eyebrow">CHAMADO #<?= (int)$id ?></p>
    <h1>Editar chamado</h1>
    <p class="muted">Atualize os dados ou o andamento da solicitação.</p>
  </div><a class="button button-secondary" href="chamados.php">Voltar</a>
</section>
<section class="panel form-panel">
  <?php if ($erros): ?><div class="alert"><?php foreach ($erros as $erro): ?><p><?= e($erro) ?></p><?php endforeach; ?></div><?php endif; ?>
  <form method="post" class="ticket-form">
    <label>Título do problema<input name="titulo" maxlength="100" required value="<?= e($chamado['titulo']) ?>"></label>
    <label>Solicitante<input name="solicitante" list="lista-solicitantes" maxlength="100" required autocomplete="off" value="<?= e($chamado['solicitante']) ?>" placeholder="Digite o nome do solicitante"></label>
    <datalist id="lista-solicitantes"><?php foreach ($usuarios as $u): ?><option value="<?= e($u['nome']) ?>"></option><?php endforeach; ?></datalist>
    <div class="form-row">
      <label>Categoria<select name="id_categoria" required><?php foreach ($categorias as $cat): ?><option value="<?= (int)$cat['id_categoria'] ?>" <?= (int)$chamado['id_categoria'] === (int)$cat['id_categoria'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?></select></label>
      <label>Prioridade<select name="prioridade"><?php foreach (['Alta', 'Media', 'Baixa'] as $op): ?><option value="<?= e($op) ?>" <?= $chamado['prioridade'] === $op ? 'selected' : '' ?>><?= e($op) ?></option><?php endforeach; ?></select></label>
    </div>
    <label>Status<select name="status"><?php foreach (['Aberto', 'Em andamento', 'Resolvido'] as $op): ?><option value="<?= e($op) ?>" <?= $chamado['status'] === $op ? 'selected' : '' ?>><?= e($op) ?></option><?php endforeach; ?></select></label>
    <label>Descrição<textarea name="descricao" rows="5" required><?= e($chamado['descricao']) ?></textarea></label>
    <div class="form-actions"><a class="text-link" href="chamados.php">Cancelar</a><button class="button" type="submit">Salvar alterações</button></div>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>