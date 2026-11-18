<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('chamados.php');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM chamados WHERE id_chamado = ?');
    $stmt->execute([$id]);
}
redirecionar('chamados.php');
