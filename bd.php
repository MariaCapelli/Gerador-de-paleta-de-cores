<?php
/**
 * Conexão com o Banco de Dados via PDO
 * Sistema: Gerador de Paleta de Cores
 * 
 * Utiliza exclusivamente PDO com Prepared Statements para segurança contra SQL Injections.
 * Executa auto-inicialização das tabelas caso não existam.
 */

// Inicia a sessão nativa do PHP caso ainda não esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$port = '3306';
$dbname = 'gerador_paletas';
$username = 'root';
$password = '';

try {
    // 1. Tenta conectar diretamente ao banco
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // Se o banco ainda não existe, conecta sem dbname e cria
        $pdoServer = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, $options);
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo = new PDO($dsn, $username, $password, $options);
    }

    // 2. Garante que as tabelas necessárias estejam criadas
    inicializarTabelas($pdo);

} catch (PDOException $e) {
    // Tratamento de erro seguro sem expor credenciais
    die("Erro ao conectar ao banco de dados: " . htmlspecialchars($e->getMessage()));
}

/**
 * Cria as tabelas do sistema se ainda não existirem
 */
function inicializarTabelas(PDO $pdo): void {
    // Tabela de Usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `senha` VARCHAR(255) NOT NULL,
            `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Paletas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `paletas` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT NOT NULL,
            `nome` VARCHAR(120) NOT NULL,
            `descricao` TEXT NULL,
            `harmonia` VARCHAR(50) NOT NULL,
            `cor_base` VARCHAR(7) NOT NULL,
            `cores` JSON NOT NULL,
            `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_paletas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Tabela de Auditoria e Histórico
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `auditoria` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT NULL,
            `usuario_nome` VARCHAR(100) NOT NULL,
            `acao` VARCHAR(50) NOT NULL,
            `registro_afetado` VARCHAR(255) NOT NULL,
            `detalhes` TEXT NULL,
            `ip_origem` VARCHAR(45) NULL,
            `data_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Cria usuário padrão de demonstração se a tabela estiver vazia
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `usuarios`");
    $total = (int)$stmt->fetch()['total'];
    if ($total === 0) {
        $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("
            INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`)
            VALUES (1, 'Maria Capelli', 'maria@paletas.com', :senha)
        ");
        $stmtInsert->execute([':senha' => $senhaHash]);
    }
}
