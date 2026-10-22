<?php
/**
 * Componentes Modais Universais
 * Sistema: Gerador de Paleta de Cores
 * 
 * Substitui completamente caixas de diálogo nativas do navegador (alert / confirm)
 * com modais estilizados em Tailwind CSS.
 */
?>

<!-- 1. Modal Universal de Confirmação / Alerta (Substitui confirm() e alert()) -->
<div id="universalModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="universalModalBox">
        <div class="flex items-start space-x-4">
            <div id="universalModalIconContainer" class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center bg-rose-500/20 text-rose-400">
                <svg id="universalModalIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 id="universalModalTitle" class="text-lg font-bold text-slate-100">Confirmação</h3>
                <div class="mt-2">
                    <p id="universalModalMessage" class="text-sm text-slate-300 leading-relaxed"></p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button id="universalModalCancelBtn" type="button" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition border border-slate-700">
                Cancelar
            </button>
            <button id="universalModalConfirmBtn" type="button" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-lg shadow-rose-600/30 transition">
                Confirmar
            </button>
        </div>
    </div>
</div>

<!-- 2. Modal de Salvar Paleta -->
<div id="savePaletteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="savePaletteModalBox">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-slate-100 flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span>Salvar Paleta de Cores</span>
            </h3>
            <button type="button" onclick="closeSaveModal()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Preview das Cores Selecionadas -->
        <div class="my-5">
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Cores da Paleta</label>
            <div id="modalPalettePreview" class="flex h-14 rounded-xl overflow-hidden shadow-inner border border-slate-700/50">
                <!-- Inserido dinamicamente via JS -->
            </div>
        </div>

        <form id="savePaletteForm" action="actions/salvar_paleta.php" method="POST" class="space-y-4">
            <input type="hidden" name="harmonia" id="modalInputHarmonia" value="analoga">
            <input type="hidden" name="cor_base" id="modalInputCorBase" value="#3B82F6">
            <input type="hidden" name="cores" id="modalInputCores" value="">

            <div>
                <label for="paletteName" class="block text-sm font-medium text-slate-300 mb-1">Nome da Paleta *</label>
                <input type="text" id="paletteName" name="nome" required placeholder="Ex: Sunset Vibes, Aurora Minimalista..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label for="paletteDescription" class="block text-sm font-medium text-slate-300 mb-1">Descrição (opcional)</label>
                <textarea id="paletteDescription" name="descricao" rows="3" placeholder="Descreva onde pretende usar esta paleta (UI, landing page, branding)..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-3">
                <button type="button" onclick="closeSaveModal()" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition border border-slate-700">
                    Cancelar
                </button>
                <button type="submit" id="btnConfirmSavePalette" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    <span>Salvar Paleta</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal de Exportação de Código -->
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-xl transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="exportModalBox">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-slate-100 flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                <span>Exportar Paleta de Cores</span>
            </h3>
            <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="mt-4 flex border-b border-slate-800 space-x-2">
            <button type="button" onclick="switchExportTab('css')" id="exportTab-css" class="export-tab active px-3.5 py-2 text-xs font-semibold rounded-t-lg border-b-2 border-indigo-500 text-indigo-400">
                CSS Variables
            </button>
            <button type="button" onclick="switchExportTab('tailwind')" id="exportTab-tailwind" class="export-tab px-3.5 py-2 text-xs font-semibold rounded-t-lg border-b-2 border-transparent text-slate-400 hover:text-slate-200">
                Tailwind CSS
            </button>
            <button type="button" onclick="switchExportTab('json')" id="exportTab-json" class="export-tab px-3.5 py-2 text-xs font-semibold rounded-t-lg border-b-2 border-transparent text-slate-400 hover:text-slate-200">
                JSON
            </button>
            <button type="button" onclick="switchExportTab('svg')" id="exportTab-svg" class="export-tab px-3.5 py-2 text-xs font-semibold rounded-t-lg border-b-2 border-transparent text-slate-400 hover:text-slate-200">
                SVG Swatch
            </button>
        </div>

        <div class="mt-4">
            <div class="relative">
                <pre class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs font-mono text-indigo-300 overflow-x-auto max-h-60" id="exportCodeOutput"></pre>
                <button type="button" onclick="copyExportCode()" class="absolute top-2.5 right-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-medium border border-slate-700 flex items-center space-x-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Copiar</span>
                </button>
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="button" onclick="closeExportModal()" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition border border-slate-700">
                Fechar
            </button>
        </div>
    </div>
</div>
