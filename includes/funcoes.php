<?php
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function redirecionar(string $destino): never
{
    header('Location: ' . $destino);
    exit;
}

/**
 * Procura um solicitante pelo nome (sem diferenciar maiusculas/acentos, conforme a collation do banco).
 * Se nao existir, cadastra automaticamente e devolve o id.
 */
function obterOuCriarUsuario(PDO $pdo, string $nome): int
{
    $nome = trim(preg_replace('/\s+/', ' ', $nome));
    $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE nome = ? LIMIT 1');
    $stmt->execute([$nome]);
    $id = $stmt->fetchColumn();
    if ($id !== false) {
        return (int)$id;
    }
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email) VALUES (?, ?)');
    $stmt->execute([$nome, '']);
    return (int)$pdo->lastInsertId();
}
