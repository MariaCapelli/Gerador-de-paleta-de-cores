<?php
/**
 * API Endpoint: Gerar Cores Harmônicas
 * Sistema: Gerador de Paleta de Cores
 * 
 * Retorna JSON com as 5 cores da harmonia solicitada com base no círculo cromático
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../services/ColorService.php';

$corBase = $_GET['cor_base'] ?? $_POST['cor_base'] ?? '#3B82F6';
$harmonia = $_GET['harmonia'] ?? $_POST['harmonia'] ?? 'analoga';

try {
    $paleta = ColorService::gerarPaleta($corBase, $harmonia);
    
    echo json_encode([
        'sucesso' => true,
        'cor_base' => ColorService::sanitizarHex($corBase),
        'harmonia' => $harmonia,
        'cores' => $paleta
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao processar cores: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
