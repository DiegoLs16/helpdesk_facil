<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirLogin();

$totais = ['Aberto' => 0, 'Em andamento' => 0, 'Resolvido' => 0];
$stmt = $pdo->query('SELECT status, COUNT(*) AS total FROM chamados GROUP BY status');
foreach ($stmt->fetchAll() as $linha) $totais[$linha['status']] = (int)$linha['total'];
$recentes = $pdo->query('SELECT c.*, u.nome AS solicitante, cat.nome AS categoria
    FROM chamados c
    INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
    INNER JOIN categorias cat ON cat.id_categoria = c.id_categoria
    ORDER BY c.data_abertura DESC LIMIT 5')->fetchAll();
$cssPagina = 'index.css';
$tituloPagina = 'Painel';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
  <div>
    <p class="eyebrow">VISÃO GERAL</p>
    <h1>Painel de chamados</h1>
    <p class="muted">Acompanhe as solicitações de suporte técnico.</p>
  </div>
  <div class="heading-actions">
    <a class="button button-secondary" href="logout.php" title="Encerrar sessão">Sair</a>
  </div>
</section>
<section class="stats">
  <article class="stat-card"><span class="stat-label">Chamados abertos</span><strong><?= $totais['Aberto'] ?></strong><span class="stat-dot amber">●</span></article>
  <article class="stat-card"><span class="stat-label">Em andamento</span><strong><?= $totais['Em andamento'] ?></strong><span class="stat-dot blue">●</span></article>
  <article class="stat-card"><span class="stat-label">Resolvidos</span><strong><?= $totais['Resolvido'] ?></strong><span class="stat-dot green">●</span></article>
</section>
<section class="panel">
  <div class="panel-heading">
    <div>
      <h2>Chamados recentes</h2>
      <p class="muted">Últimas solicitações cadastradas</p>
    </div><a class="text-link" href="chamados.php">Ver todos →</a>
  </div>
  <?php if (!$recentes): ?>
    <div class="empty">Nenhum chamado cadastrado. <a href="novo.php">Cadastre o primeiro.</a></div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Chamado</th>
            <th>Solicitante</th>
            <th>Prioridade</th>
            <th>Status</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentes as $c): ?>
            <tr>
              <td><a class="ticket-title" href="editar.php?id=<?= (int)$c['id_chamado'] ?>">#<?= (int)$c['id_chamado'] ?> · <?= e($c['titulo']) ?></a><small><?= e($c['categoria']) ?></small></td>
              <td><?= e($c['solicitante']) ?></td>
              <td><span class="priority priority-<?= strtolower(e($c['prioridade'])) ?>"><?= e($c['prioridade']) ?></span></td>
              <td><span class="status status-<?= $c['status'] === 'Em andamento' ? 'andamento' : strtolower($c['status']) ?>"><?= e($c['status']) ?></span></td>
              <td><?= date('d/m/Y H:i', strtotime($c['data_abertura'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>