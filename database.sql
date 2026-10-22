-- Esquema do Banco de Dados: Gerador de Paletas de Cores
-- Compatível com MySQL / MariaDB (XAMPP)

CREATE DATABASE IF NOT EXISTS `gerador_paletas` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `gerador_paletas`;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Paletas
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

-- Tabela de Auditoria e Histórico de Ações
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

-- Índices para otimização de consultas
CREATE INDEX IF NOT EXISTS `idx_paletas_usuario` ON `paletas` (`usuario_id`);
CREATE INDEX IF NOT EXISTS `idx_auditoria_data` ON `auditoria` (`data_hora` DESC);
CREATE INDEX IF NOT EXISTS `idx_auditoria_acao` ON `auditoria` (`acao`);

-- Inserção do usuário padrão caso não exista (Senha: admin123)
INSERT IGNORE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `criado_em`)
VALUES (1, 'Maria Capelli', 'maria@paletas.com', '$2y$10$wO3l.8pUoZJgQv85hWnZle8Qj16V5jXvYk3o5qT8sC1pQzZlP1j3e', NOW());
