<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirLogin();

$retorno = ($_POST['retorno'] ?? '') === 'index' ? 'index.php' : 'chamados.php';

// Somente o administrador altera o status, e apenas por POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    if ($id && in_array($status, ['Aberto', 'Em andamento', 'Resolvido'], true)) {
        $stmt = $pdo->prepare('UPDATE chamados SET status = ? WHERE id_chamado = ?');
        $stmt->execute([$status, $id]);
    }

    // Mantém a busca que estava ativa na lista de chamados.
    $busca = trim($_POST['busca'] ?? '');
    if ($retorno === 'chamados.php' && $busca !== '') {
        $retorno .= '?busca=' . urlencode($busca);
    }
}

redirecionar($retorno);
