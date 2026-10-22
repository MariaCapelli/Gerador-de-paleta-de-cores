/**
 * Gerenciador da Interface do Gerador de Cores
 * Sistema: Gerador de Paleta de Cores
 */

let currentBaseColor = '#3B82F6';
let currentHarmony = 'analoga';
let currentPalette = [];
let colorWheel = null;

document.addEventListener('DOMContentLoaded', () => {
    initGenerator();
});

function initGenerator() {
    // 1. Inicializa o Círculo Cromático
    colorWheel = new ColorWheel('chromaticWheelCanvas', {
        size: 300,
        baseColor: currentBaseColor,
        harmony: currentHarmony,
        onChange: (newHex) => {
            updateBaseColor(newHex);
        }
    });

    // 2. Eventos dos seletores de harmonia
    const harmonyTabs = document.querySelectorAll('.harmony-btn');
    harmonyTabs.forEach(btn => {
        btn.addEventListener('click', (e) => {
            harmonyTabs.forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-indigo-600/30', 'border-indigo-500');
                b.classList.add('bg-slate-800/80', 'text-slate-300', 'border-slate-700/80');
            });
            btn.classList.remove('bg-slate-800/80', 'text-slate-300', 'border-slate-700/80');
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-indigo-600/30', 'border-indigo-500');

            currentHarmony = btn.getAttribute('data-harmony');
            loadPalette();
        });
    });

    // 3. Eventos do input de cor nativo e input texto HEX
    const baseColorPicker = document.getElementById('baseColorPicker');
    const baseColorInput = document.getElementById('baseColorInput');

    if (baseColorPicker) {
        baseColorPicker.addEventListener('input', (e) => {
            updateBaseColor(e.target.value);
        });
    }

    if (baseColorInput) {
        baseColorInput.addEventListener('change', (e) => {
            let val = e.target.value.trim();
            if (!val.startsWith('#')) val = '#' + val;
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                updateBaseColor(val);
            } else {
                window.showToast('Código HEX inválido. Use o formato #RRGGBB', 'aviso');
                baseColorInput.value = currentBaseColor;
            }
        });
    }

    // 4. Sliders RGB
    ['redRange', 'greenRange', 'blueRange'].forEach(id => {
        const slider = document.getElementById(id);
        if (slider) {
            slider.addEventListener('input', updateFromRgbSliders);
        }
    });

    // 5. Botão Aleatório
    const randomBtn = document.getElementById('randomizeBtn');
    if (randomBtn) {
        randomBtn.addEventListener('click', randomizePalette);
    }

    // 6. Carrega paleta inicial
    loadPalette();
}

/**
 * Atualiza a cor base e sincroniza inputs
 */
function updateBaseColor(hex) {
    currentBaseColor = hex.toUpperCase();
    const baseColorPicker = document.getElementById('baseColorPicker');
    const baseColorInput = document.getElementById('baseColorInput');

    if (baseColorPicker) baseColorPicker.value = currentBaseColor;
    if (baseColorInput) baseColorInput.value = currentBaseColor;

    updateRgbSlidersFromHex(currentBaseColor);
    loadPalette();
}

/**
 * Faz requisição à API para gerar as 5 cores da harmonia
 */
function loadPalette() {
    fetch(`api/generate.php?cor_base=${encodeURIComponent(currentBaseColor)}&harmonia=${encodeURIComponent(currentHarmony)}`)
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                currentPalette = data.cores;
                renderPaletteCards(currentPalette);
                if (colorWheel) {
                    colorWheel.setColors(currentPalette);
                }
            } else {
                window.showToast(data.mensagem || 'Erro ao gerar paleta.', 'erro');
            }
        })
        .catch(err => {
            console.error('Erro na API:', err);
            window.showToast('Erro de comunicação com o servidor.', 'erro');
        });
}

/**
 * Renderiza os 5 cartões de cores (Swatches) estilo Adobe Color
 */
function renderPaletteCards(palette) {
    const container = document.getElementById('paletteCardsContainer');
    if (!container) return;

    container.innerHTML = '';

    palette.forEach((color, index) => {
        const card = document.createElement('div');
        card.className = 'swatch-card flex flex-col rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 shadow-xl';

        card.innerHTML = `
            <!-- Superfície de Cor -->
            <div class="h-44 sm:h-52 w-full relative p-4 flex flex-col justify-between transition-colors duration-200" style="background-color: ${color.hex}; color: ${color.textColor};">
                <div class="flex items-center justify-between">
                    ${color.isBase ? `
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase shadow-sm bg-black/40 text-white backdrop-blur-sm border border-white/20">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span>Cor Base</span>
                        </span>
                    ` : `
                        <button type="button" onclick="setAsBaseColor('${color.hex}')" title="Definir como cor base" class="opacity-75 hover:opacity-100 hover:scale-105 p-1.5 rounded-full bg-black/20 hover:bg-black/40 backdrop-blur-sm text-xs font-semibold text-white transition flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            <span class="text-[10px]">Tornar Base</span>
                        </button>
                    `}
                    <span class="text-xs font-mono font-medium opacity-70">#${index + 1}</span>
                </div>

                <!-- Botão de Cópia Rápida Central -->
                <button type="button" onclick="window.copyToClipboard('${color.hex}', 'HEX')" class="self-center bg-black/30 hover:bg-black/50 text-white backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-mono font-bold tracking-wider opacity-90 hover:opacity-100 hover:scale-105 transition shadow-sm border border-white/10 flex items-center space-x-1.5">
                    <span>${color.hex}</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>

                <div class="text-[10px] opacity-75 font-mono text-center">
                    Clique para copiar HEX
                </div>
            </div>

            <!-- Dados Técnicos: HEX, RGB, HSL -->
            <div class="p-4 space-y-2.5 text-xs">
                <!-- Linha HEX -->
                <div class="flex items-center justify-between py-1 border-b border-slate-800/80">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">HEX</span>
                    <button type="button" onclick="window.copyToClipboard('${color.hex}', 'HEX')" class="font-mono text-slate-200 hover:text-indigo-400 font-medium flex items-center space-x-1 group">
                        <span>${color.hex}</span>
                        <svg class="w-3 h-3 text-slate-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <!-- Linha RGB -->
                <div class="flex items-center justify-between py-1 border-b border-slate-800/80">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">RGB</span>
                    <button type="button" onclick="window.copyToClipboard('${color.rgb}', 'RGB')" class="font-mono text-slate-200 hover:text-indigo-400 font-medium flex items-center space-x-1 group text-[11px]">
                        <span>${color.rgb}</span>
                        <svg class="w-3 h-3 text-slate-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <!-- Linha HSL -->
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">HSL</span>
                    <button type="button" onclick="window.copyToClipboard('${color.hsl}', 'HSL')" class="font-mono text-slate-200 hover:text-indigo-400 font-medium flex items-center space-x-1 group text-[11px]">
                        <span>${color.hsl}</span>
                        <svg class="w-3 h-3 text-slate-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(card);
    });
}

function setAsBaseColor(hex) {
    updateBaseColor(hex);
    window.showToast(`Nova cor base definida: ${hex}`, 'info');
}

/**
 * Atualiza sliders RGB com base no valor HEX
 */
function updateRgbSlidersFromHex(hex) {
    const r = parseInt(hex.slice(1, 3), 16) || 0;
    const g = parseInt(hex.slice(3, 5), 16) || 0;
    const b = parseInt(hex.slice(5, 7), 16) || 0;

    const redR = document.getElementById('redRange');
    const greenR = document.getElementById('greenRange');
    const blueR = document.getElementById('blueRange');

    const redVal = document.getElementById('redVal');
    const greenVal = document.getElementById('greenVal');
    const blueVal = document.getElementById('blueVal');

    if (redR) redR.value = r;
    if (greenR) greenR.value = g;
    if (blueR) blueR.value = b;

    if (redVal) redVal.textContent = r;
    if (greenVal) greenVal.textContent = g;
    if (blueVal) blueVal.textContent = b;
}

function updateFromRgbSliders() {
    const r = parseInt(document.getElementById('redRange').value, 10);
    const g = parseInt(document.getElementById('greenRange').value, 10);
    const b = parseInt(document.getElementById('blueRange').value, 10);

    document.getElementById('redVal').textContent = r;
    document.getElementById('greenVal').textContent = g;
    document.getElementById('blueVal').textContent = b;

    const hex = "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
    currentBaseColor = hex;

    const baseColorPicker = document.getElementById('baseColorPicker');
    const baseColorInput = document.getElementById('baseColorInput');
    if (baseColorPicker) baseColorPicker.value = hex;
    if (baseColorInput) baseColorInput.value = hex;

    loadPalette();
}

/**
 * Gera uma nova paleta com cor base aleatória
 */
function randomizePalette() {
    const randomHex = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0').toUpperCase();
    const harmonias = ['analoga', 'monocromatica', 'triade', 'complementar', 'complementar_dividida', 'tetrade', 'homologa'];
    const randomHarmonia = harmonias[Math.floor(Math.random() * harmonias.length)];

    // Atualiza botão de harmonia
    const harmonyTabs = document.querySelectorAll('.harmony-btn');
    harmonyTabs.forEach(btn => {
        if (btn.getAttribute('data-harmony') === randomHarmonia) {
            btn.classList.remove('bg-slate-800/80', 'text-slate-300', 'border-slate-700/80');
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-indigo-600/30', 'border-indigo-500');
        } else {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-indigo-600/30', 'border-indigo-500');
            btn.classList.add('bg-slate-800/80', 'text-slate-300', 'border-slate-700/80');
        }
    });

    currentHarmony = randomHarmonia;
    updateBaseColor(randomHex);
    window.showToast('Nova paleta harmônica gerada!', 'sucesso');
}

/**
 * Abre o Modal de Salvar Paleta
 */
window.openSaveModal = function() {
    if (!currentPalette || currentPalette.length === 0) {
        window.showToast('Gere uma paleta antes de salvar.', 'aviso');
        return;
    }

    const modal = document.getElementById('savePaletteModal');
    const box = document.getElementById('savePaletteModalBox');
    const previewContainer = document.getElementById('modalPalettePreview');

    document.getElementById('modalInputHarmonia').value = currentHarmony;
    document.getElementById('modalInputCorBase').value = currentBaseColor;
    document.getElementById('modalInputCores').value = currentPalette.map(c => c.hex).join(',');

    // Renderiza preview no modal
    previewContainer.innerHTML = '';
    currentPalette.forEach(c => {
        const item = document.createElement('div');
        item.className = 'flex-1 h-full';
        item.style.backgroundColor = c.hex;
        previewContainer.appendChild(item);
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
};

window.closeSaveModal = function() {
    const modal = document.getElementById('savePaletteModal');
    const box = document.getElementById('savePaletteModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
};

/**
 * Modal de Exportação
 */
window.openExportModal = function() {
    if (!currentPalette || currentPalette.length === 0) {
        window.showToast('Nenhuma paleta para exportar.', 'aviso');
        return;
    }

    const modal = document.getElementById('exportModal');
    const box = document.getElementById('exportModalBox');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);

    switchExportTab('css');
};

window.closeExportModal = function() {
    const modal = document.getElementById('exportModal');
    const box = document.getElementById('exportModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
};

window.switchExportTab = function(format) {
    const tabs = document.querySelectorAll('.export-tab');
    tabs.forEach(t => {
        t.classList.remove('border-indigo-500', 'text-indigo-400');
        t.classList.add('border-transparent', 'text-slate-400');
    });

    const activeTab = document.getElementById(`exportTab-${format}`);
    if (activeTab) {
        activeTab.classList.remove('border-transparent', 'text-slate-400');
        activeTab.classList.add('border-indigo-500', 'text-indigo-400');
    }

    const output = document.getElementById('exportCodeOutput');
    const hexList = currentPalette.map(c => c.hex);

    if (format === 'css') {
        let code = `:root {\n`;
        hexList.forEach((hex, i) => {
            code += `  --color-${i + 1}: ${hex};\n`;
        });
        code += `}`;
        output.textContent = code;
    } else if (format === 'tailwind') {
        let code = `// tailwind.config.js\nmodule.exports = {\n  theme: {\n    extend: {\n      colors: {\n        palette: {\n`;
        hexList.forEach((hex, i) => {
            code += `          '${(i + 1) * 100}': '${hex}',\n`;
        });
        code += `        }\n      }\n    }\n  }\n}`;
        output.textContent = code;
    } else if (format === 'json') {
        output.textContent = JSON.stringify({
            cor_base: currentBaseColor,
            harmonia: currentHarmony,
            cores: hexList
        }, null, 2);
    } else if (format === 'svg') {
        output.textContent = `<svg width="500" height="100" xmlns="http://www.w3.org/2000/svg">\n` +
            hexList.map((hex, i) => `  <rect x="${i * 100}" width="100" height="100" fill="${hex}" />`).join('\n') +
            `\n</svg>`;
    }
};

window.copyExportCode = function() {
    const output = document.getElementById('exportCodeOutput');
    if (output) {
        window.copyToClipboard(output.textContent, 'Código exportado');
    }
};
