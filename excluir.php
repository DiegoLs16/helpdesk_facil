<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('chamados.php');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('SELECT id_chamado, id_criador FROM chamados WHERE id_chamado = ?');
    $stmt->execute([$id]);
    $chamado = $stmt->fetch();

    if ($chamado && !podeGerenciarChamado($chamado)) {
        definirFlash('erro', 'Você só pode excluir os chamados que você mesmo abriu.');
    } elseif ($chamado) {
        $stmt = $pdo->prepare('DELETE FROM chamados WHERE id_chamado = ?');
        $stmt->execute([$id]);
        definirFlash('ok', 'Chamado excluído.');
    }
}
redirecionar('chamados.php');
