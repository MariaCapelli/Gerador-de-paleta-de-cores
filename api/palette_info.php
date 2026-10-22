<?php
/**
 * API Endpoint: Obter detalhes da paleta por ID
 * Sistema: Gerador de Paleta de Cores
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/PaletteService.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

$paleta = PaletteService::buscarPorId($pdo, $id);

if (!$paleta) {
    http_response_code(404);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Paleta não encontrada.']);
    exit;
}

// Constrói descritores completos para cada cor
$coresDetalhadas = [];
foreach ($paleta['cores_array'] as $hex) {
    $coresDetalhadas[] = ColorService::formatarDescritorCor($hex, $hex === $paleta['cor_base']);
}
$paleta['cores_detalhadas'] = $coresDetalhadas;

echo json_encode([
    'sucesso' => true,
    'paleta' => $paleta
], JSON_UNESCAPED_UNICODE);
