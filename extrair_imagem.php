<?php
/**
 * Extrair Paleta a Partir de Imagem (Estilo Adobe Color / Create from Image)
 * Sistema: Gerador de Paleta de Cores
 */

$pageTitle = 'Extrair Paleta de Imagem';
require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-8">
    <!-- Topo: Título e Descrição -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Extrair Paleta de Imagem</span>
                <span class="text-xs font-normal text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-full border border-indigo-800/60 font-mono">
                    Image Sampler
                </span>
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-2xl">
                Arraste uma foto ou faça upload para extrair automaticamente uma paleta harmônica com 5 cores. Arraste os círculos numerados sobre a imagem para amostrar qualquer ponto.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="salvarPaletaDaImagem()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <span>Salvar Paleta da Imagem</span>
            </button>
        </div>
    </div>

    <!-- Área de Upload / Dropzone -->
    <div id="imageDropArea" class="border-2 border-dashed border-slate-700/80 hover:border-indigo-500 rounded-3xl p-6 sm:p-8 text-center bg-slate-900/50 backdrop-blur-sm transition cursor-pointer relative">
        <input type="file" id="imageFileInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-200">Clique para enviar uma foto ou arraste o arquivo aqui</p>
                <p class="text-xs text-slate-500 mt-0.5">PNG, JPG, WEBP até 15MB</p>
            </div>
        </div>
    </div>

    <!-- Visualizador de Imagem com Marcadores Interativos -->
    <div class="flex flex-col items-center justify-center bg-slate-900/60 p-4 sm:p-6 rounded-3xl border border-slate-800 shadow-2xl">
        <div class="relative overflow-hidden rounded-2xl shadow-2xl border border-slate-700/80 inline-block max-w-full">
            <canvas id="imageCanvas" class="block max-w-full h-auto"></canvas>
            <div id="markersOverlay" class="absolute inset-0 pointer-events-auto"></div>
        </div>
        <p class="mt-3 text-xs text-slate-400 text-center flex items-center space-x-1">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
            <span>Arraste os marcadores numerados sobre a foto para capturar novas cores</span>
        </p>
    </div>

    <!-- Swatches Extraídos -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <span>Cores Amostradas da Imagem</span>
                <span class="text-xs font-normal text-slate-400 font-mono">(5 Tons Extraídos)</span>
            </h2>
        </div>
        <div id="extractedSwatchesContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Renderizado dinamicamente via image-extractor.js -->
        </div>
    </div>
</div>

<script src="assets/js/image-extractor.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
