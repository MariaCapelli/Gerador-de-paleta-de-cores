<?php
/**
 * Página: Login
 * Sistema: Gerador de Paleta de Cores
 */

$pageTitle = 'Entrar na Conta';
require_once __DIR__ . '/bd.php';
require_once __DIR__ . '/services/AuthService.php';

// Se já estiver logado, redireciona para a home
if (AuthService::isLogado()) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-8">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-pink-500 mx-auto flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-100">Acesse sua Conta</h2>
            <p class="text-xs text-slate-400 mt-1">Gerencie suas paletas e acompanhe o histórico</p>
        </div>

        <form action="actions/auth.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="login">

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="senha" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="••••••••" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition">
                Entrar
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800/80 space-y-3">
            <!-- Acesso de Demonstração Rápido -->
            <a href="actions/auth.php?acao=demo" class="w-full flex items-center justify-center space-x-2 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-xs border border-slate-700 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Entrar como Usuário Demo (1 Clique)</span>
            </a>

            <div class="text-center text-xs text-slate-400">
                Não possui conta? 
                <a href="registrar.php" class="text-indigo-400 hover:underline font-semibold">Cadastre-se</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
