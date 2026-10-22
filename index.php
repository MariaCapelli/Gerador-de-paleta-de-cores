<?php
/**
 * Página Principal: Gerador de Paleta de Cores
 * Sistema: Gerador de Paleta de Cores
 * 
 * Interface inspirada no Adobe Color com Círculo Cromático interativo,
 * seleção de harmonias, swatches com HEX, RGB, HSL e cópia rápida.
 */

$pageTitle = 'Círculo Cromático & Gerador de Cores';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/services/ColorService.php';

$harmonias = ColorService::getHarmoniasDisponiveis();
?>

<div class="space-y-8">
    <!-- Top Bar: Título, Descrição e Ações Principais -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Roda de Cores & Harmonias</span>
                <span class="text-xs font-normal text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-full border border-indigo-800/60 font-mono">
                    Círculo Cromático
                </span>
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-2xl">
                Selecione uma cor base e explore combinações harmônicas baseadas na teoria das cores e no círculo cromático. Copie códigos HEX, RGB ou HSL em um clique.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Botão Aleatório -->
            <button id="randomizeBtn" type="button" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium border border-slate-700 transition flex items-center space-x-1.5 shadow-sm">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Aleatório</span>
            </button>

            <!-- Botão Exportar Código -->
            <button type="button" onclick="openExportModal()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium border border-slate-700 transition flex items-center space-x-1.5 shadow-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                <span>Exportar</span>
            </button>

            <!-- Botão Salvar Paleta -->
            <button type="button" onclick="openSaveModal()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Salvar Paleta</span>
            </button>
        </div>
    </div>

    <!-- Seção de Seleção de Harmonia (Pills Responsivos) -->
    <div class="overflow-x-auto pb-2 scrollbar-none">
        <div class="flex items-center space-x-2 min-w-max">
            <?php foreach ($harmonias as $chave => $info): ?>
                <button type="button" 
                        data-harmony="<?= $chave ?>"
                        title="<?= htmlspecialchars($info['descricao']) ?>"
                        class="harmony-btn px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 border flex items-center space-x-2
                        <?= $chave === 'analoga' ? 'bg-indigo-600 text-white shadow-indigo-600/30 border-indigo-500' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-800 border-slate-700/80' ?>">
                    <span><?= htmlspecialchars($info['nome']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Grid: Círculo Cromático & Controles Interativos -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-slate-900/60 p-6 sm:p-8 rounded-3xl border border-slate-800/80 shadow-2xl backdrop-blur-sm">
        
        <!-- Coluna Esquerda: Círculo Cromático Interativo -->
        <div class="lg:col-span-5 flex flex-col items-center justify-center">
            <div class="relative p-2 rounded-full bg-slate-950/80 border border-slate-800 shadow-2xl shadow-indigo-500/5 color-wheel-container">
                <canvas id="chromaticWheelCanvas" class="cursor-crosshair rounded-full"></canvas>
            </div>
            <p class="mt-3 text-xs text-slate-400 text-center flex items-center space-x-1">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                <span>Clique e arraste no círculo para girar a harmonia</span>
            </p>
        </div>

        <!-- Coluna Direita: Controles de Ajuste da Cor Base (HEX, RGB e HSL) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-slate-950/70 p-5 sm:p-6 rounded-2xl border border-slate-800/80 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <span class="text-sm font-bold text-slate-200 uppercase tracking-wider">Ajuste Fino da Cor Base</span>
                    <span class="text-xs text-slate-400 font-mono">Modo RGB & Sliders</span>
                </div>

                <!-- Input HEX e Color Picker Nativo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Código HEX</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="baseColorInput" value="#3B82F6" maxlength="7" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-sm font-mono text-slate-100 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold tracking-wider">
                            <div class="relative w-10 h-10 flex-shrink-0">
                                <input type="color" id="baseColorPicker" value="#3B82F6" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                <div class="w-full h-full rounded-xl border border-slate-600 shadow-sm flex items-center justify-center bg-indigo-600 pointer-events-none" style="background-color: #3B82F6;" id="baseColorPreviewBox">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 4 4 0 014-4c2 0 2 2 4 2 1.5 0 2.5-.5 3.5-1.5 1-1 1.5-2 1.5-3.5 0-2-2-4-2-4a4 4 0 014-4 4 4 0 014 4c0 3-1 6-4 9-2 2-4 3-7 3z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-400 bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                        <span class="font-medium text-slate-300 block mb-0.5">Harmonia Ativa:</span>
                        <span id="activeHarmonyInfo" class="text-indigo-400 font-semibold">Análoga</span>
                        <p class="text-[11px] text-slate-400 mt-1">Gera tons adjacentes que transmitem serenidade visual e conforto óptico.</p>
                    </div>
                </div>

                <!-- Sliders RGB -->
                <div class="space-y-3 pt-2">
                    <!-- Red Slider -->
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-1">
                            <span class="text-rose-400 font-bold">R (Vermelho)</span>
                            <span id="redVal" class="text-slate-300">59</span>
                        </div>
                        <input type="range" id="redRange" min="0" max="255" value="59" class="w-full accent-rose-500">
                    </div>

                    <!-- Green Slider -->
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-1">
                            <span class="text-emerald-400 font-bold">G (Verde)</span>
                            <span id="greenVal" class="text-slate-300">130</span>
                        </div>
                        <input type="range" id="greenRange" min="0" max="255" value="130" class="w-full accent-emerald-500">
                    </div>

                    <!-- Blue Slider -->
                    <div>
                        <div class="flex justify-between text-xs font-mono mb-1">
                            <span class="text-sky-400 font-bold">B (Azul)</span>
                            <span id="blueVal" class="text-slate-300">246</span>
                        </div>
                        <input type="range" id="blueRange" min="0" max="255" value="246" class="w-full accent-sky-500">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Cartões da Paleta (5 Cores - Mobile-First Grid) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <span>Cores da Paleta Gerada</span>
                <span class="text-xs font-normal text-slate-400 font-mono">(5 Cores Harmônicas)</span>
            </h2>
            <span class="text-xs text-slate-400 hidden sm:inline">
                Clique nos códigos para copiar instantaneamente
            </span>
        </div>

        <div id="paletteCardsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Renderizado dinamicamente via generator.js -->
        </div>
    </div>
</div>

<!-- Scripts específicos do gerador -->
<script src="assets/js/color-wheel.js"></script>
<script src="assets/js/generator.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
