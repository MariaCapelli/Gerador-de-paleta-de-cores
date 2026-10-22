<?php
/**
 * Componente Global de Toasts
 * Sistema: Gerador de Paleta de Cores
 * 
 * Exibe mensagens claras, elegantes e não obstrusivas ao usuário
 */

require_once __DIR__ . '/../services/AuthService.php';

$flash = AuthService::getFlash();
?>

<!-- Container fixo para notificações Toast -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    <?php if ($flash): ?>
        <div class="toast-item pointer-events-auto transform transition-all duration-300 ease-out translate-y-0 opacity-100 flex items-start p-4 rounded-xl shadow-2xl border backdrop-blur-md 
            <?= $flash['tipo'] === 'sucesso' ? 'bg-emerald-950/90 border-emerald-500/50 text-emerald-100' : '' ?>
            <?= $flash['tipo'] === 'erro' ? 'bg-rose-950/90 border-rose-500/50 text-rose-100' : '' ?>
            <?= $flash['tipo'] === 'aviso' ? 'bg-amber-950/90 border-amber-500/50 text-amber-100' : '' ?>
            <?= $flash['tipo'] === 'info' ? 'bg-sky-950/90 border-sky-500/50 text-sky-100' : '' ?>"
            role="alert"
            data-auto-dismiss="5000">
            
            <div class="flex-shrink-0 mr-3">
                <?php if ($flash['tipo'] === 'sucesso'): ?>
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php elseif ($flash['tipo'] === 'erro'): ?>
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php elseif ($flash['tipo'] === 'aviso'): ?>
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <?php else: ?>
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php endif; ?>
            </div>

            <div class="flex-1 text-sm font-medium">
                <?= htmlspecialchars($flash['texto']) ?>
            </div>

            <button type="button" class="ml-3 text-slate-400 hover:text-white transition-colors" onclick="this.closest('.toast-item').remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    <?php endif; ?>
</div>
