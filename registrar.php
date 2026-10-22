<?php
/**
 * Página: Cadastro de Usuário
 * Sistema: Gerador de Paleta de Cores
 */

$pageTitle = 'Criar Nova Conta';
require_once __DIR__ . '/bd.php';
require_once __DIR__ . '/services/AuthService.php';

if (AuthService::isLogado()) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-8">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-indigo-500 mx-auto flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-100">Criar Nova Conta</h2>
            <p class="text-xs text-slate-400 mt-1">Junte-se para salvar e auditar suas paletas</p>
        </div>

        <form action="actions/auth.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="registrar">

            <div>
                <label for="nome" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nome Completo</label>
                <input type="text" id="nome" name="nome" required placeholder="Ex: Maria Capelli" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="senha" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Senha (mínimo 6 caracteres)</label>
                <input type="password" id="senha" name="senha" minlength="6" required placeholder="••••••••" class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-lg shadow-emerald-600/30 transition">
                Cadastrar
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800/80 text-center text-xs text-slate-400">
            Já possui uma conta? 
            <a href="login.php" class="text-indigo-400 hover:underline font-semibold">Fazer Login</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
