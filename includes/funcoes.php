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

function obterOuCriarUsuario(PDO $pdo, string $nome): int
{
    $nome = trim(preg_replace('/\s+/', ' ', $nome));
    $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE nome = ? LIMIT 1');
    $stmt->execute([$nome]);
    $id = $stmt->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email) VALUES (?, ?)');
    $stmt->execute([$nome, '']);
    return (int) $pdo->lastInsertId();
}

function login(PDO $pdo, string $email, string $senha): bool
{
    $stmt = $pdo->prepare("SELECT id_usuario, nome, senha, permissao FROM usuarios WHERE email = ?");
    $stmt->execute([trim($email)]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['permissao'] = $usuario['permissao'];
        return true;
    }
    return false;

}

function exigirLogin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }
}

function isAdmin(): bool
{
    return ($_SESSION['permissao'] ?? '') === 'Administrador';
}
