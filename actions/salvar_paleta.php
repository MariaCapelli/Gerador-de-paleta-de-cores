<?php
/**
 * Ação: Salvar Paleta
 * Sistema: Gerador de Paleta de Cores
 */

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/PaletteService.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
          || isset($_POST['is_ajax']);

// Se não estiver logado, faz auto-login do usuário demo para facilitar a navegação ou exige login
if (!AuthService::isLogado()) {
    AuthService::loginDemo($pdo);
}

$usuario = AuthService::getUsuarioLogado();
if (!$usuario) {
    if ($isAjax) {
        http_response_code(401);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado para salvar paletas.']);
        exit;
    }
    AuthService::setFlash('erro', 'Você precisa estar logado para salvar paletas.');
    header('Location: ../login.php');
    exit;
}

// Suporte para JSON payload no corpo da requisição ou $_POST
$rawBody = file_get_contents('php://input');
$jsonData = json_decode($rawBody, true);

if ($jsonData) {
    $nome = $jsonData['nome'] ?? '';
    $descricao = $jsonData['descricao'] ?? null;
    $harmonia = $jsonData['harmonia'] ?? 'analoga';
    $corBase = $jsonData['cor_base'] ?? '#3B82F6';
    $cores = $jsonData['cores'] ?? [];
} else {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? null;
    $harmonia = $_POST['harmonia'] ?? 'analoga';
    $corBase = $_POST['cor_base'] ?? '#3B82F6';
    $coresRaw = $_POST['cores'] ?? '';
    $cores = is_array($coresRaw) ? $coresRaw : explode(',', $coresRaw);
}

$resultado = PaletteService::salvar(
    $pdo,
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
    header('Location: ../paletas.php');
} else {
    AuthService::setFlash('erro', $resultado['mensagem']);
    header('Location: ../index.php');
}
exit;
