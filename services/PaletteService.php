<?php
/**
 * Serviço de Gestão de Paletas
 * Sistema: Gerador de Paleta de Cores
 * 
 * Centraliza as operações de CRUD no banco de dados via PDO e aciona a auditoria.
 */

require_once __DIR__ . '/AuditService.php';
require_once __DIR__ . '/ColorService.php';

class PaletteService {

    /**
     * Salva uma nova paleta vinculada ao usuário
     */
    public static function salvar(PDO $pdo, int $usuarioId, string $nome, ?string $descricao, string $harmonia, string $corBase, array $cores): array {
        $nome = trim(strip_tags($nome));
        $descricao = $descricao ? trim(strip_tags($descricao)) : null;
        $harmonia = trim($harmonia);
        $corBase = ColorService::sanitizarHex($corBase);

        if (empty($nome)) {
            return ['sucesso' => false, 'mensagem' => 'O nome da paleta é obrigatório.'];
        }

        // Sanitiza array de cores em formato HEX
        $coresSanitizadas = array_map(function($c) {
            return is_array($c) && isset($c['hex']) ? ColorService::sanitizarHex($c['hex']) : ColorService::sanitizarHex((string)$c);
        }, $cores);

        if (count($coresSanitizadas) < 2) {
            return ['sucesso' => false, 'mensagem' => 'A paleta precisa conter pelo menos 2 cores harmônicas.'];
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO `paletas` (`usuario_id`, `nome`, `descricao`, `harmonia`, `cor_base`, `cores`, `criado_em`)
                VALUES (:usuario_id, :nome, :descricao, :harmonia, :cor_base, :cores, NOW())
            ");

            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':harmonia' => $harmonia,
                ':cor_base' => $corBase,
                ':cores' => json_encode($coresSanitizadas)
            ]);

            $paletaId = (int)$pdo->lastInsertId();

            // Auditoria obrigatória
            AuditService::registrar(
                $pdo,
                'Criação',
                "Paleta #{$paletaId} - {$nome}",
                json_encode([
                    'paleta_id' => $paletaId,
                    'nome' => $nome,
                    'harmonia' => $harmonia,
                    'cor_base' => $corBase,
                    'cores' => $coresSanitizadas
                ], JSON_UNESCAPED_UNICODE)
            );

            return ['sucesso' => true, 'id' => $paletaId, 'mensagem' => 'Paleta salva com sucesso!'];
        } catch (PDOException $e) {
            error_log("Erro ao salvar paleta: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => 'Erro interno ao salvar paleta: ' . $e->getMessage()];
        }
    }

    /**
     * Atualiza uma paleta existente
     */
    public static function atualizar(PDO $pdo, int $paletaId, int $usuarioId, string $nome, ?string $descricao, string $harmonia, string $corBase, array $cores): array {
        $nome = trim(strip_tags($nome));
        $descricao = $descricao ? trim(strip_tags($descricao)) : null;
        $harmonia = trim($harmonia);
        $corBase = ColorService::sanitizarHex($corBase);

        if (empty($nome)) {
            return ['sucesso' => false, 'mensagem' => 'O nome da paleta é obrigatório.'];
        }

        // Verifica existência e permissão (ou permissão de admin)
        $stmtExist = $pdo->prepare("SELECT * FROM `paletas` WHERE `id` = :id");
        $stmtExist->execute([':id' => $paletaId]);
        $anterior = $stmtExist->fetch();

        if (!$anterior) {
            return ['sucesso' => false, 'mensagem' => 'Paleta não encontrada.'];
        }

        if ((int)$anterior['usuario_id'] !== $usuarioId && $usuarioId !== 1) {
            return ['sucesso' => false, 'mensagem' => 'Você não tem permissão para editar esta paleta.'];
        }

        $coresSanitizadas = array_map(function($c) {
            return is_array($c) && isset($c['hex']) ? ColorService::sanitizarHex($c['hex']) : ColorService::sanitizarHex((string)$c);
        }, $cores);

        try {
            $stmt = $pdo->prepare("
                UPDATE `paletas` 
                SET `nome` = :nome, 
                    `descricao` = :descricao, 
                    `harmonia` = :harmonia, 
                    `cor_base` = :cor_base, 
                    `cores` = :cores,
                    `atualizado_em` = NOW()
                WHERE `id` = :id
            ");

            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':harmonia' => $harmonia,
                ':cor_base' => $corBase,
                ':cores' => json_encode($coresSanitizadas),
                ':id' => $paletaId
            ]);

            // Auditoria detalhando a alteração
            AuditService::registrar(
                $pdo,
                'Edição',
                "Paleta #{$paletaId} - {$nome}",
                json_encode([
                    'paleta_id' => $paletaId,
                    'antes' => [
                        'nome' => $anterior['nome'],
                        'harmonia' => $anterior['harmonia'],
                        'cor_base' => $anterior['cor_base'],
                        'cores' => json_decode($anterior['cores'], true)
                    ],
                    'depois' => [
                        'nome' => $nome,
                        'harmonia' => $harmonia,
                        'cor_base' => $corBase,
                        'cores' => $coresSanitizadas
                    ]
                ], JSON_UNESCAPED_UNICODE)
            );

            return ['sucesso' => true, 'mensagem' => 'Paleta atualizada com sucesso!'];
        } catch (PDOException $e) {
            error_log("Erro ao atualizar paleta: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar a paleta.'];
        }
    }

    /**
     * Exclui uma paleta e registra na auditoria
     */
    public static function excluir(PDO $pdo, int $paletaId, int $usuarioId): array {
        $stmtExist = $pdo->prepare("SELECT * FROM `paletas` WHERE `id` = :id");
        $stmtExist->execute([':id' => $paletaId]);
        $paleta = $stmtExist->fetch();

        if (!$paleta) {
            return ['sucesso' => false, 'mensagem' => 'Paleta não encontrada.'];
        }

        if ((int)$paleta['usuario_id'] !== $usuarioId && $usuarioId !== 1) {
            return ['sucesso' => false, 'mensagem' => 'Você não tem permissão para excluir esta paleta.'];
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM `paletas` WHERE `id` = :id");
            $stmt->execute([':id' => $paletaId]);

            // Auditoria obrigatória
            AuditService::registrar(
                $pdo,
                'Exclusão',
                "Paleta #{$paletaId} - {$paleta['nome']}",
                json_encode([
                    'paleta_id' => $paletaId,
                    'nome' => $paleta['nome'],
                    'harmonia' => $paleta['harmonia'],
                    'cor_base' => $paleta['cor_base'],
                    'cores' => json_decode($paleta['cores'], true)
                ], JSON_UNESCAPED_UNICODE)
            );

            return ['sucesso' => true, 'mensagem' => 'Paleta excluída com sucesso!'];
        } catch (PDOException $e) {
            error_log("Erro ao excluir paleta: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => 'Erro interno ao excluir paleta.'];
        }
    }

    /**
     * Busca uma paleta pelo ID
     */
    public static function buscarPorId(PDO $pdo, int $paletaId): ?array {
        $stmt = $pdo->prepare("
            SELECT p.*, u.nome as autor_nome, u.email as autor_email 
            FROM `paletas` p 
            LEFT JOIN `usuarios` u ON p.usuario_id = u.id 
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $paletaId]);
        $paleta = $stmt->fetch();

        if ($paleta) {
            $paleta['cores_array'] = json_decode($paleta['cores'], true) ?: [];
        }

        return $paleta ?: null;
    }

    /**
     * Lista paletas com filtros e paginação
     */
    public static function listar(PDO $pdo, array $filtros = []): array {
        $sql = "
            SELECT p.*, u.nome as autor_nome, u.email as autor_email 
            FROM `paletas` p 
            LEFT JOIN `usuarios` u ON p.usuario_id = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND p.usuario_id = :usuario_id";
            $params[':usuario_id'] = (int)$filtros['usuario_id'];
        }

        if (!empty($filtros['harmonia'])) {
            $sql .= " AND p.harmonia = :harmonia";
            $params[':harmonia'] = $filtros['harmonia'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :buscaDesc)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
            $params[':buscaDesc'] = '%' . $filtros['busca'] . '%';
        }

        $sql .= " ORDER BY p.criado_em DESC";

        if (!empty($filtros['limite'])) {
            $sql .= " LIMIT " . (int)$filtros['limite'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $paletas = $stmt->fetchAll();

        foreach ($paletas as &$p) {
            $p['cores_array'] = json_decode($p['cores'], true) ?: [];
        }

        return $paletas;
    }
}
