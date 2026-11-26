<?php
function e($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function iniciarSessao(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

/**
 * Procura o solicitante pelo nome; se não existir, cadastra.
 * O solicitante criado por aqui não consegue fazer login: o e-mail fica vazio
 * e a senha é um hash aleatório que ninguém conhece.
 */
function obterOuCriarUsuario(PDO $pdo, string $nome): int
{
    $nome = trim(preg_replace('/\s+/', ' ', $nome));
    $stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE nome = ? LIMIT 1');
    $stmt->execute([$nome]);
    $id = $stmt->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }
    $senhaInutilizavel = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, permissao, telefone) VALUES (?, '', ?, 'Usuario', NULL)");
    $stmt->execute([$nome, $senhaInutilizavel]);
    return (int) $pdo->lastInsertId();
}

function login(PDO $pdo, string $email, string $senha): bool
{
    $email = trim($email);
    if ($email === '' || $senha === '') {
        return false;
    }

    $stmt = $pdo->prepare('SELECT id_usuario, nome, senha, permissao FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        return false;
    }

    $ok = false;
    if (password_verify($senha, $usuario['senha'])) {
        $ok = true;
        $novoHash = password_needs_rehash($usuario['senha'], PASSWORD_DEFAULT)
            ? password_hash($senha, PASSWORD_DEFAULT) : null;
    } elseif (empty(password_get_info($usuario['senha'])['algo']) && hash_equals($usuario['senha'], $senha)) {
        // Senha antiga guardada em texto puro (banco de versões anteriores): converte para hash.
        $ok = true;
        $novoHash = password_hash($senha, PASSWORD_DEFAULT);
    }

    if (!$ok) {
        return false;
    }
    if (!empty($novoHash)) {
        $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id_usuario = ?')->execute([$novoHash, $usuario['id_usuario']]);
    }

    iniciarSessao();
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['permissao'] = $usuario['permissao'];
    return true;
}

function exigirLogin(): void
{
    iniciarSessao();
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }
}

function isAdmin(): bool
{
    return ($_SESSION['permissao'] ?? '') === 'Administrador';
}

function classeStatus(string $status): string
{
    return $status === 'Em andamento' ? 'andamento' : strtolower($status);
}

/**
 * Célula de status das listas.
 * Administrador: lista de opções que salva sozinha ao escolher (atualizar_status.php).
 * Demais usuários: apenas o selo colorido.
 * $retorno: 'index' ou 'chamados' (página para onde voltar). $busca: filtro atual da lista.
 */
function celulaStatus(array $chamado, string $retorno, string $busca = ''): string
{
    $status = (string) $chamado['status'];
    $classe = classeStatus($status);

    if (!isAdmin()) {
        return '<span class="status status-' . $classe . '">' . e($status) . '</span>';
    }

    $id = (int) $chamado['id_chamado'];
    $html  = '<form method="post" action="atualizar_status.php" class="form-status">';
    $html .= '<input type="hidden" name="id" value="' . $id . '">';
    $html .= '<input type="hidden" name="retorno" value="' . e($retorno) . '">';
    $html .= '<input type="hidden" name="busca" value="' . e($busca) . '">';
    $html .= '<select name="status" class="status status-select status-' . $classe . '"'
           . ' aria-label="Status do chamado #' . $id . '" onchange="this.form.submit()">';
    foreach (['Aberto', 'Em andamento', 'Resolvido'] as $opcao) {
        $html .= '<option value="' . e($opcao) . '"' . ($opcao === $status ? ' selected' : '') . '>' . e($opcao) . '</option>';
    }
    $html .= '</select><noscript><button type="submit" class="link-danger">OK</button></noscript></form>';
    return $html;
}

/**
 * Regra de permissão: o administrador gerencia qualquer chamado;
 * os demais usuários só gerenciam (editam e excluem) os chamados que eles mesmos abriram.
 */
function podeGerenciarChamado(array $chamado): bool
{
    if (isAdmin()) {
        return true;
    }
    return isset($_SESSION['id_usuario'])
        && !empty($chamado['id_criador'])
        && (int) $chamado['id_criador'] === (int) $_SESSION['id_usuario'];
}

/** Título do chamado: link para editar se a pessoa pode gerenciá-lo; texto simples se não puder. */
function tituloChamado(array $chamado): string
{
    $texto = '#' . (int) $chamado['id_chamado'] . ' · ' . e($chamado['titulo']);
    if (!podeGerenciarChamado($chamado)) {
        return '<span class="ticket-title">' . $texto . '</span>';
    }
    return '<a class="ticket-title" href="editar.php?id=' . (int) $chamado['id_chamado'] . '">' . $texto . '</a>';
}

/** Guarda uma mensagem para aparecer uma vez no topo da próxima página. $tipo: 'erro' ou 'ok'. */
function definirFlash(string $tipo, string $texto): void
{
    iniciarSessao();
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}
