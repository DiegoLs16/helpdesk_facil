<?php
$host = 'localhost';
$porta = 3306;
$banco = 'helpdesk_facil';
$usuario = 'root';
$senha = '';

$opcoesPdo = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $criarBanco = function () use ($host, $porta, $usuario, $senha, $opcoesPdo) {
        $admin = new PDO("mysql:host={$host};port={$porta};charset=utf8mb4", $usuario, $senha, $opcoesPdo);
        $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents(__DIR__ . '/../database.sql'));
        foreach (explode(';', $sql) as $comando) {
            if (trim($comando) !== '') {
                $admin->exec($comando);
            }
        }
    };

    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4",
            $usuario,
            $senha,
            $opcoesPdo
        );
    } catch (PDOException $e) {
        if ((int) $e->getCode() !== 1049 && strpos($e->getMessage(), '1049') === false) {
            throw $e;
        }
        $criarBanco();
        $pdo = new PDO(
            "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4",
            $usuario,
            $senha,
            $opcoesPdo
        );
    }

    // Banco existe, mas sem as tabelas (ex.: importação interrompida): cria tudo.
    if (!$pdo->query("SHOW TABLES LIKE 'usuarios'")->fetch()) {
        $criarBanco();
    }

    $coluna = $pdo->query("SHOW COLUMNS FROM chamados LIKE 'id_criador'")->fetch();
    if (!$coluna) {
        $pdo->exec('ALTER TABLE chamados ADD COLUMN id_criador INT NULL');
        $pdo->exec("UPDATE chamados c INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
                    SET c.id_criador = c.id_usuario WHERE u.email <> ''");
        try {
            $pdo->exec('ALTER TABLE chamados ADD CONSTRAINT fk_chamados_criador FOREIGN KEY (id_criador)
                        REFERENCES usuarios (id_usuario) ON DELETE SET NULL');
        } catch (PDOException $ignorado) {
        }
    }
} catch (PDOException $e) {
    header('Content-Type: text/html; charset=utf-8');
    exit('Não foi possível conectar ao banco de dados. Confira config/conexao.php e se o MySQL está iniciado no XAMPP.'
        . '<br><small>Detalhe técnico: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</small>');
}
