<?php
/**
 * Ação: Autenticação de Usuários
 * Sistema: Gerador de Paleta de Cores
 */

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/AuthService.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $res = AuthService::login($pdo, $email, $senha);
        if ($res['sucesso']) {
            AuthService::setFlash('sucesso', $res['mensagem']);
            header('Location: ../index.php');
        } else {
            AuthService::setFlash('erro', $res['mensagem']);
            header('Location: ../login.php');
        }
        exit;

    case 'registrar':
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $res = AuthService::registrar($pdo, $nome, $email, $senha);
        if ($res['sucesso']) {
            AuthService::setFlash('sucesso', $res['mensagem']);
            header('Location: ../index.php');
        } else {
            AuthService::setFlash('erro', $res['mensagem']);
            header('Location: ../registrar.php');
        }
        exit;

    case 'demo':
        AuthService::loginDemo($pdo);
        AuthService::setFlash('sucesso', 'Conectado como usuário de demonstração!');
        header('Location: ../index.php');
        exit;

    case 'logout':
        AuthService::logout();
        AuthService::setFlash('info', 'Sessão encerrada com sucesso.');
        header('Location: ../login.php');
        exit;

    default:
        header('Location: ../index.php');
        exit;
}
