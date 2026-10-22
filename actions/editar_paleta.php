<?php
/**
 * Ação: Editar Paleta
 * Sistema: Gerador de Paleta de Cores
 */

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/PaletteService.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
          || isset($_POST['is_ajax']);

if (!AuthService::isLogado()) {
    AuthService::loginDemo($pdo);
}

$usuario = AuthService::getUsuarioLogado();
if (!$usuario) {
    if ($isAjax) {
        http_response_code(401);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
        exit;
    }
    AuthService::setFlash('erro', 'Você precisa estar logado.');
    header('Location: ../login.php');
    exit;
}

$rawBody = file_get_contents('php://input');
$jsonData = json_decode($rawBody, true);

if ($jsonData) {
    $paletaId = (int)($jsonData['id'] ?? 0);
    $nome = $jsonData['nome'] ?? '';
    $descricao = $jsonData['descricao'] ?? null;
    $harmonia = $jsonData['harmonia'] ?? 'analoga';
    $corBase = $jsonData['cor_base'] ?? '#3B82F6';
    $cores = $jsonData['cores'] ?? [];
} else {
    $paletaId = (int)($_POST['id'] ?? 0);
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? null;
    $harmonia = $_POST['harmonia'] ?? 'analoga';
    $corBase = $_POST['cor_base'] ?? '#3B82F6';
    $coresRaw = $_POST['cores'] ?? '';
    $cores = is_array($coresRaw) ? $coresRaw : explode(',', $coresRaw);
}

$resultado = PaletteService::atualizar(
    $pdo,
    $paletaId,
    $usuario['id'],
    $nome,
    $descricao,
    $harmonia,
    $corBase,
    $cores
);

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resultado);
    exit;
}

if ($resultado['sucesso']) {
    AuthService::setFlash('sucesso', $resultado['mensagem']);
} else {
    AuthService::setFlash('erro', $resultado['mensagem']);
}
header('Location: ../paletas.php');
exit;
