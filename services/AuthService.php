<?php
/**
 * Serviço de Autenticação e Gestão de Sessões
 * Sistema: Gerador de Paleta de Cores
 * 
 * Gerencia login, cadastro e controle de sessão nativa em PHP
 */

class AuthService {

    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica se há um usuário autenticado
     */
    public static function isLogado(): bool {
        self::iniciar();
        return !empty($_SESSION['usuario_id']);
    }

    /**
     * Retorna os dados do usuário atual na sessão
     */
    public static function getUsuarioLogado(): ?array {
        self::iniciar();
        if (!self::isLogado()) {
            return null;
        }
        return [
            'id' => $_SESSION['usuario_id'],
            'nome' => $_SESSION['usuario_nome'] ?? 'Usuário',
            'email' => $_SESSION['usuario_email'] ?? ''
        ];
    }

    /**
     * Exige que o usuário esteja logado; se não estiver, redireciona para login
     */
    public static function exigirLogin(string $redirect = 'login.php'): void {
        if (!self::isLogado()) {
            self::setFlash('aviso', 'Você precisa estar logado para acessar esta página.');
            header("Location: {$redirect}");
            exit;
        }
    }

    /**
     * Realiza o login com email e senha
     */
    public static function login(PDO $pdo, string $email, string $senha): array {
        self::iniciar();
        $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));

        if (empty($email) || empty($senha)) {
            return ['sucesso' => false, 'mensagem' => 'Preencha todos os campos.'];
        }

        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM `usuarios` WHERE `email` = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return ['sucesso' => false, 'mensagem' => 'E-mail ou senha inválidos.'];
        }

        // Salva na sessão
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];

        return ['sucesso' => true, 'mensagem' => "Bem-vindo de volta, {$usuario['nome']}!"];
    }

    /**
     * Cadastra um novo usuário
     */
    public static function registrar(PDO $pdo, string $nome, string $email, string $senha): array {
        self::iniciar();
        $nome = trim(strip_tags($nome));
        $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));

        if (empty($nome) || empty($email) || empty($senha)) {
            return ['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['sucesso' => false, 'mensagem' => 'Por favor, informe um e-mail válido.'];
        }

        if (strlen($senha) < 6) {
            return ['sucesso' => false, 'mensagem' => 'A senha deve ter no mínimo 6 caracteres.'];
        }

        // Verifica duplicidade de e-mail
        $stmtCheck = $pdo->prepare("SELECT id FROM `usuarios` WHERE `email` = :email LIMIT 1");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->fetch()) {
            return ['sucesso' => false, 'mensagem' => 'Este e-mail já está cadastrado. Tente fazer login.'];
        }

        // Insere novo usuário
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO `usuarios` (`nome`, `email`, `senha`) VALUES (:nome, :email, :senha)");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $hash
        ]);

        $novoId = (int)$pdo->lastInsertId();
        $_SESSION['usuario_id'] = $novoId;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;

        return ['sucesso' => true, 'mensagem' => "Conta criada com sucesso! Bem-vindo, {$nome}."];
    }

    /**
     * Login rápido como usuário de demonstração
     */
    public static function loginDemo(PDO $pdo): bool {
        self::iniciar();
        $stmt = $pdo->query("SELECT id, nome, email FROM `usuarios` ORDER BY id ASC LIMIT 1");
        $usuario = $stmt->fetch();
        if ($usuario) {
            $_SESSION['usuario_id'] = (int)$usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            return true;
        }
        return false;
    }

    /**
     * Encerra a sessão atual
     */
    public static function logout(): void {
        self::iniciar();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Define mensagem flash na sessão para ser exibida via Toast
     */
    public static function setFlash(string $tipo, string $texto): void {
        self::iniciar();
        $_SESSION['flash_message'] = [
            'tipo' => $tipo, // 'sucesso', 'erro', 'aviso', 'info'
            'texto' => $texto
        ];
    }

    /**
     * Recupera e limpa a mensagem flash da sessão
     */
    public static function getFlash(): ?array {
        self::iniciar();
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }
}
