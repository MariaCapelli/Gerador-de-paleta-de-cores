<?php
/**
 * Logout
 * Sistema: Gerador de Paleta de Cores
 */

require_once __DIR__ . '/bd.php';
require_once __DIR__ . '/services/AuthService.php';

AuthService::logout();
AuthService::setFlash('info', 'Sessão encerrada com sucesso.');
header('Location: login.php');
exit;
