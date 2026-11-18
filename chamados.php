<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$busca = trim($_GET['busca'] ?? '');
$sql = 'SELECT c.*, u.nome AS solicitante, cat.nome AS categoria
        FROM chamados c
        INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
        INNER JOIN categorias cat ON cat.id_categoria = c.id_categoria';
if ($busca !== '') {
    $sql .= ' WHERE c.titulo LIKE :busca OR u.nome LIKE :busca OR cat.nome LIKE :busca';
    $sql .= ' ORDER BY c.data_abertura DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['busca' => '%' . $busca . '%']);
    $chamados = $stmt->fetchAll();
} else {
    $chamados = $pdo->query($sql . ' ORDER BY c.data_abertura DESC')->fetchAll();
}
$cssPagina = 'chamados.css';
$tituloPagina = 'Chamados';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
  <div><p class="eyebrow">ATENDIMENTO</p><h1>Todos os chamados</h1><p class="muted">Consulte e gerencie as solicitações registradas.</p></div>
  <a class="button" href="novo.php">+ Novo chamado</a>
</section>
<section class="panel">
  <form class="search-form" method="get" action="chamados.php">
    <input type="search" name="busca" placeholder="Buscar por título, solicitante ou categoria..." value="<?= e($busca) ?>">
    <button class="button button-secondary" type="submit">Buscar</button>
    <?php if ($busca !== ''): ?><a class="text-link" href="chamados.php">Limpar</a><?php endif; ?>
  </form>
  <?php if (!$chamados): ?><div class="empty">Nenhum chamado encontrado.</div><?php else: ?>
  <div class="table-wrap"><table><thead><tr><th>ID / Chamado</th><th>Solicitante</th><th>Prioridade</th><th>Status</th><th>Data</th><th>Ações</th></tr></thead><tbody>
  <?php foreach ($chamados as $c): ?>
    <tr>
      <td><a class="ticket-title" href="editar.php?id=<?= (int)$c['id_chamado'] ?>">#<?= (int)$c['id_chamado'] ?> · <?= e($c['titulo']) ?></a><small><?= e($c['categoria']) ?></small></td>
      <td><?= e($c['solicitante']) ?></td>
      <td><span class="priority priority-<?= strtolower(e($c['prioridade'])) ?>"><?= e($c['prioridade']) ?></span></td>
      <td><span class="status status-<?= $c['status'] === 'Em andamento' ? 'andamento' : strtolower($c['status']) ?>"><?= e($c['status']) ?></span></td>
      <td><?= date('d/m/Y H:i', strtotime($c['data_abertura'])) ?></td>
      <td class="actions"><a href="editar.php?id=<?= (int)$c['id_chamado'] ?>">Editar</a>
        <form method="post" action="excluir.php" onsubmit="return confirm('Tem certeza que deseja excluir este chamado?');">
          <input type="hidden" name="id" value="<?= (int)$c['id_chamado'] ?>"><button class="link-danger" type="submit">Excluir</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
