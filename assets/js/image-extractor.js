/**
 * Extrator de Cores de Imagem (Estilo Adobe Color / Create from Image)
 * Sistema: Gerador de Paleta de Cores
 * 
 * Permite carregar uma imagem, posicionar marcadores de amostragem
 * e extrair automaticamente 5 cores da paleta com suporte a salvar.
 */

let uploadedImage = null;
let imageCanvas = null;
let imageCtx = null;
let extractedColors = [];
let markers = [];

document.addEventListener('DOMContentLoaded', () => {
    initImageExtractor();
});

function initImageExtractor() {
    imageCanvas = document.getElementById('imageCanvas');
    if (!imageCanvas) return;
    imageCtx = imageCanvas.getContext('2d');

    const fileInput = document.getElementById('imageFileInput');
    const dropArea = document.getElementById('imageDropArea');

    if (fileInput) {
        fileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                loadImage(e.target.files[0]);
            }
        });
    }

    if (dropArea) {
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-indigo-500', 'bg-indigo-500/10');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-indigo-500', 'bg-indigo-500/10');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-indigo-500', 'bg-indigo-500/10');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                loadImage(e.dataTransfer.files[0]);
            }
        });
    }

    // Carrega imagem de exemplo inicial
    loadSampleImage();
}

function loadSampleImage() {
    const img = new Image();
    img.crossOrigin = "anonymous";
    img.onload = () => {
        uploadedImage = img;
        drawCanvasAndExtract();
    };
    // Imagem decorativa de alta qualidade
    img.src = 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=800&auto=format&fit=crop&q=80';
}

function loadImage(file) {
    if (!file.type.startsWith('image/')) {
        window.showToast('Por favor, envie um arquivo de imagem válido.', 'erro');
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            uploadedImage = img;
            drawCanvasAndExtract();
            window.showToast('Imagem carregada com sucesso!', 'sucesso');
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function drawCanvasAndExtract() {
    if (!uploadedImage || !imageCanvas) return;

    const maxWidth = 750;
    let width = uploadedImage.naturalWidth || uploadedImage.width;
    let height = uploadedImage.naturalHeight || uploadedImage.height;

    if (width > maxWidth) {
        height = Math.round((height * maxWidth) / width);
        width = maxWidth;
    }

    imageCanvas.width = width;
    imageCanvas.height = height;
    imageCtx.drawImage(uploadedImage, 0, 0, width, height);

    // Inicializa 5 pontos de amostragem padrão
    markers = [
        { x: width * 0.20, y: height * 0.30 },
        { x: width * 0.35, y: height * 0.65 },
        { x: width * 0.50, y: height * 0.40 },
        { x: width * 0.65, y: height * 0.70 },
        { x: width * 0.80, y: height * 0.35 }
    ];

    sampleColorsFromMarkers();
    initMarkerDragging();
}

function sampleColorsFromMarkers() {
    extractedColors = markers.map(m => {
        const pixel = imageCtx.getImageData(Math.round(m.x), Math.round(m.y), 1, 1).data;
        const hex = rgbToHex(pixel[0], pixel[1], pixel[2]);
        const hsl = rgbToHsl(pixel[0], pixel[1], pixel[2]);
        const textColor = (pixel[0]*0.299 + pixel[1]*0.587 + pixel[2]*0.114) > 140 ? '#0F172A' : '#FFFFFF';

        return {
            hex: hex,
            rgb: `rgb(${pixel[0]}, ${pixel[1]}, ${pixel[2]})`,
            hsl: `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`,
            textColor: textColor
        };
    });

    renderExtractedSwatches();
    renderMarkersOverlay();
}

function renderExtractedSwatches() {
    const container = document.getElementById('extractedSwatchesContainer');
    if (!container) return;

    container.innerHTML = '';
    extractedColors.forEach((color, idx) => {
        const swatch = document.createElement('div');
        swatch.className = 'swatch-card flex flex-col rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 shadow-xl';

        swatch.innerHTML = `
            <div class="h-36 sm:h-44 w-full p-3 flex flex-col justify-between" style="background-color: ${color.hex}; color: ${color.textColor};">
                <span class="text-xs font-mono font-bold opacity-80">Amostra #${idx + 1}</span>
                <button type="button" onclick="window.copyToClipboard('${color.hex}', 'HEX')" class="self-center bg-black/30 hover:bg-black/50 text-white backdrop-blur-md px-3 py-1 rounded-lg text-xs font-mono font-bold tracking-wider transition border border-white/10">
                    ${color.hex}
                </button>
                <span class="text-[10px] text-center opacity-70">Copiar</span>
            </div>
            <div class="p-3 text-xs space-y-2">
                <div class="flex items-center justify-between border-b border-slate-800 pb-1">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">RGB</span>
                    <button type="button" onclick="window.copyToClipboard('${color.rgb}', 'RGB')" class="font-mono text-slate-200 hover:text-indigo-400 text-[11px]">
                        ${color.rgb}
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">HSL</span>
                    <button type="button" onclick="window.copyToClipboard('${color.hsl}', 'HSL')" class="font-mono text-slate-200 hover:text-indigo-400 text-[11px]">
                        ${color.hsl}
                    </button>
                </div>
            </div>
        `;
        container.appendChild(swatch);
    });
}

function renderMarkersOverlay() {
    const overlay = document.getElementById('markersOverlay');
    if (!overlay) return;

    overlay.innerHTML = '';
    markers.forEach((m, idx) => {
        const pin = document.createElement('div');
        pin.className = 'absolute w-7 h-7 rounded-full border-2 border-white shadow-xl flex items-center justify-center text-xs font-bold transform -translate-x-1/2 -translate-y-1/2 cursor-grab active:cursor-grabbing hover:scale-110 transition-transform';
        pin.style.left = `${m.x}px`;
        pin.style.top = `${m.y}px`;
        pin.style.backgroundColor = extractedColors[idx]?.hex || '#ffffff';
        pin.style.color = extractedColors[idx]?.textColor || '#000000';
        pin.textContent = (idx + 1).toString();
        pin.setAttribute('data-index', idx);

        overlay.appendChild(pin);
    });
}

function initMarkerDragging() {
    const overlay = document.getElementById('markersOverlay');
    if (!overlay) return;

    let activeMarker = null;

    const onStart = (e) => {
        const target = e.target.closest('[data-index]');
        if (!target) return;
        activeMarker = parseInt(target.getAttribute('data-index'), 10);
        e.preventDefault();
    };

    const onMove = (e) => {
        if (activeMarker === null) return;
        const rect = overlay.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        const x = Math.max(0, Math.min(imageCanvas.width, clientX - rect.left));
        const y = Math.max(0, Math.min(imageCanvas.height, clientY - rect.top));

        markers[activeMarker] = { x, y };
        sampleColorsFromMarkers();
    };

    const onEnd = () => {
        activeMarker = null;
    };

    overlay.addEventListener('mousedown', onStart);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);

    overlay.addEventListener('touchstart', onStart, { passive: false });
    window.addEventListener('touchmove', onMove, { passive: false });
    window.addEventListener('touchend', onEnd);
}

window.salvarPaletaDaImagem = function() {
    if (!extractedColors || extractedColors.length === 0) {
        window.showToast('Nenhuma cor extraída para salvar.', 'aviso');
        return;
    }

    const modal = document.getElementById('savePaletteModal');
    const box = document.getElementById('savePaletteModalBox');
    const previewContainer = document.getElementById('modalPalettePreview');

    document.getElementById('modalInputHarmonia').value = 'extraida_de_imagem';
    document.getElementById('modalInputCorBase').value = extractedColors[0].hex;
    document.getElementById('modalInputCores').value = extractedColors.map(c => c.hex).join(',');

    previewContainer.innerHTML = '';
    extractedColors.forEach(c => {
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

function rgbToHex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
}

function rgbToHsl(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;
    if (max === min) {
        h = s = 0;
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}
