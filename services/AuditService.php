<?php
/**
 * Serviço de Auditoria e Histórico de Ações
 * Sistema: Gerador de Paleta de Cores
 * 
 * Registra formalmente todas as operações de criação, edição e exclusão de paletas,
 * garantindo rastreabilidade e histórico em conformidade com as regras de negócio.
 */

require_once __DIR__ . '/AuthService.php';

class AuditService {

    /**
     * Registra um evento de auditoria no banco de dados via PDO
     */
    public static function registrar(PDO $pdo, string $acao, string $registroAfetado, ?string $detalhes = null): bool {
        try {
            $usuario = AuthService::getUsuarioLogado();
            $usuarioId = $usuario ? $usuario['id'] : null;
            $usuarioNome = $usuario ? $usuario['nome'] : 'Sistema / Visitante';

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            $stmt = $pdo->prepare("
                INSERT INTO `auditoria` (`usuario_id`, `usuario_nome`, `acao`, `registro_afetado`, `detalhes`, `ip_origem`, `data_hora`)
                VALUES (:usuario_id, :usuario_nome, :acao, :registro_afetado, :detalhes, :ip_origem, NOW())
            ");

            return $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':usuario_nome' => $usuarioNome,
                ':acao' => $acao,
                ':registro_afetado' => $registroAfetado,
                ':detalhes' => $detalhes,
                ':ip_origem' => $ip
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao registrar auditoria: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista registros de auditoria com opções de filtros
     */
    public static function listar(PDO $pdo, array $filtros = [], int $limite = 50, int $offset = 0): array {
        $sql = "SELECT a.*, u.email as usuario_email 
                FROM `auditoria` a 
                LEFT JOIN `usuarios` u ON a.usuario_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['acao'])) {
            $sql .= " AND a.acao = :acao";
            $params[':acao'] = $filtros['acao'];
        }

        if (!empty($filtros['usuario'])) {
            $sql .= " AND (a.usuario_nome LIKE :usuario OR u.email LIKE :usuarioEmail)";
            $params[':usuario'] = '%' . $filtros['usuario'] . '%';
            $params[':usuarioEmail'] = '%' . $filtros['usuario'] . '%';
        }

        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND DATE(a.data_hora) >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= " AND DATE(a.data_hora) <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (a.registro_afetado LIKE :busca OR a.detalhes LIKE :buscaDetalhes)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
            $params[':buscaDetalhes'] = '%' . $filtros['busca'] . '%';
        }

        $sql .= " ORDER BY a.data_hora DESC LIMIT " . (int)$limite . " OFFSET " . (int)$offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retorna a contagem total para paginação
     */
    public static function contar(PDO $pdo, array $filtros = []): int {
        $sql = "SELECT COUNT(*) as total FROM `auditoria` a LEFT JOIN `usuarios` u ON a.usuario_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filtros['acao'])) {
            $sql .= " AND a.acao = :acao";
            $params[':acao'] = $filtros['acao'];
        }

        if (!empty($filtros['usuario'])) {
            $sql .= " AND (a.usuario_nome LIKE :usuario OR u.email LIKE :usuarioEmail)";
            $params[':usuario'] = '%' . $filtros['usuario'] . '%';
            $params[':usuarioEmail'] = '%' . $filtros['usuario'] . '%';
        }

        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND DATE(a.data_hora) >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= " AND DATE(a.data_hora) <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (a.registro_afetado LIKE :busca OR a.detalhes LIKE :buscaDetalhes)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
            $params[':buscaDetalhes'] = '%' . $filtros['busca'] . '%';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['total'];
    }

    /**
     * Retorna estatísticas de auditoria para o dashboard
     */
    public static function obterEstatisticas(PDO $pdo): array {
        $stats = [
            'total' => 0,
            'criacoes' => 0,
            'edicoes' => 0,
            'exclusoes' => 0
        ];

        try {
            $stmt = $pdo->query("SELECT acao, COUNT(*) as qtd FROM `auditoria` GROUP BY acao");
            while ($row = $stmt->fetch()) {
                $acao = mb_strtolower($row['acao'], 'UTF-8');
                $qtd = (int)$row['qtd'];
                $stats['total'] += $qtd;

                if (str_contains($acao, 'cria')) {
                    $stats['criacoes'] += $qtd;
                } elseif (str_contains($acao, 'edi')) {
                    $stats['edicoes'] += $qtd;
                } elseif (str_contains($acao, 'exclu')) {
                    $stats['exclusoes'] += $qtd;
                }
            }
        } catch (PDOException $e) {
            error_log("Erro ao carregar estatísticas de auditoria: " . $e->getMessage());
        }

        return $stats;
    }
}
