<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirLogin();

$retorno = ($_POST['retorno'] ?? '') === 'index' ? 'index.php' : 'chamados.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    if ($id && in_array($status, ['Aberto', 'Em andamento', 'Resolvido'], true)) {
        $stmt = $pdo->prepare('UPDATE chamados SET status = ? WHERE id_chamado = ?');
        $stmt->execute([$status, $id]);
    }

    $busca = trim($_POST['busca'] ?? '');
    if ($retorno === 'chamados.php' && $busca !== '') {
        $retorno .= '?busca=' . urlencode($busca);
    }
}

redirecionar($retorno);
