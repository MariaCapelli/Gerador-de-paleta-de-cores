/**
 * Círculo Cromático Interativo (Estilo Adobe Color)
 * Sistema: Gerador de Paleta de Cores
 * 
 * Desenha o círculo de matizes e saturações via Canvas 2D
 * e gerencia os ponteiros harmônicos com suporte a arrastar e soltar (drag & drop / touch).
 */

class ColorWheel {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        this.ctx = this.canvas.getContext('2d');
        this.container = this.canvas.parentElement;

        this.size = options.size || 280;
        this.canvas.width = this.size;
        this.canvas.height = this.size;
        this.radius = this.size / 2;
        this.center = { x: this.radius, y: this.radius };

        this.baseColor = options.baseColor || '#3B82F6';
        this.harmony = options.harmony || 'analoga';
        this.paletteColors = [];
        this.isDragging = false;
        this.onChange = options.onChange || (() => {});

        this.initWheel();
        this.initEvents();
    }

    /**
     * Renderiza o fundo gradiente do círculo cromático
     */
    initWheel() {
        const imgData = this.ctx.createImageData(this.size, this.size);
        const data = imgData.data;

        for (let y = 0; y < this.size; y++) {
            for (let x = 0; x < this.size; x++) {
                const dx = x - this.center.x;
                const dy = y - this.center.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const index = (y * this.size + x) * 4;

                if (dist <= this.radius - 2) {
                    let angle = Math.atan2(dy, dx) * (180 / Math.PI);
                    if (angle < 0) angle += 360;

                    const sat = Math.min(1, dist / (this.radius - 2));
                    const rgb = this.hslToRgb(angle, sat * 100, 50);

                    data[index] = rgb.r;
                    data[index + 1] = rgb.g;
                    data[index + 2] = rgb.b;
                    data[index + 3] = 255;
                } else {
                    data[index + 3] = 0; // Transparente fora do círculo
                }
            }
        }

        this.wheelData = imgData;
        this.render();
    }

    /**
     * Renderiza o círculo com as conexões e ponteiros
     */
    render() {
        this.ctx.clearRect(0, 0, this.size, this.size);
        this.ctx.putImageData(this.wheelData, 0, 0);

        if (!this.paletteColors || this.paletteColors.length === 0) return;

        // Desenha linhas conectando ao centro ou entre os pontos
        this.ctx.lineWidth = 1.5;
        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
        this.ctx.setLineDash([3, 3]);

        this.paletteColors.forEach(item => {
            const pos = this.hslToCoords(item.hslValues.h, item.hslValues.s);
            this.ctx.beginPath();
            this.ctx.moveTo(this.center.x, this.center.y);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
        });

        this.ctx.setLineDash([]);

        // Desenha os ponteiros no canvas
        this.paletteColors.forEach((item) => {
            const pos = this.hslToCoords(item.hslValues.h, item.hslValues.s);
            const isBase = item.isBase;

            // Círculo externo
            this.ctx.beginPath();
            this.ctx.arc(pos.x, pos.y, isBase ? 11 : 8, 0, Math.PI * 2);
            this.ctx.fillStyle = item.hex;
            this.ctx.fill();

            // Borda do marcador
            this.ctx.lineWidth = isBase ? 3 : 2;
            this.ctx.strokeStyle = '#FFFFFF';
            this.ctx.stroke();

            // Ponto central branco na cor base
            if (isBase) {
                this.ctx.beginPath();
                this.ctx.arc(pos.x, pos.y, 3, 0, Math.PI * 2);
                this.ctx.fillStyle = '#FFFFFF';
                this.ctx.fill();
            }
        });
    }

    /**
     * Converte HSL em coordenadas cartesianas (X, Y) no Canvas
     */
    hslToCoords(hue, sat) {
        const rad = (hue * Math.PI) / 180;
        const dist = (sat / 100) * (this.radius - 12);
        return {
            x: this.center.x + Math.cos(rad) * dist,
            y: this.center.y + Math.sin(rad) * dist
        };
    }

    /**
     * Converte coordenadas cartesianas do Canvas em HSL
     */
    coordsToHsl(x, y) {
        const dx = x - this.center.x;
        const dy = y - this.center.y;
        let angle = Math.atan2(dy, dx) * (180 / Math.PI);
        if (angle < 0) angle += 360;

        const dist = Math.sqrt(dx * dx + dy * dy);
        const sat = Math.min(100, Math.max(10, (dist / (this.radius - 12)) * 100));

        return { h: Math.round(angle), s: Math.round(sat), l: 50 };
    }

    initEvents() {
        const handleInteraction = (e) => {
            const rect = this.canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const x = clientX - rect.left;
            const y = clientY - rect.top;

            const hsl = this.coordsToHsl(x, y);
            const rgb = this.hslToRgb(hsl.h, hsl.s, hsl.l);
            const hex = this.rgbToHex(rgb.r, rgb.g, rgb.b);

            this.onChange(hex);
        };

        const onDown = (e) => {
            this.isDragging = true;
            handleInteraction(e);
            e.preventDefault();
        };

        const onMove = (e) => {
            if (!this.isDragging) return;
            handleInteraction(e);
            e.preventDefault();
        };

        const onUp = () => {
            this.isDragging = false;
        };

        this.canvas.addEventListener('mousedown', onDown);
        window.addEventListener('mousemove', onMove);
        window.addEventListener('mouseup', onUp);

        this.canvas.addEventListener('touchstart', onDown, { passive: false });
        window.addEventListener('touchmove', onMove, { passive: false });
        window.addEventListener('touchend', onUp);
    }

    setColors(palette) {
        this.paletteColors = palette;
        this.render();
    }

    hslToRgb(h, s, l) {
        s /= 100;
        l /= 100;
        const k = n => (n + h / 30) % 12;
        const a = s * Math.min(l, 1 - l);
        const f = n => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
        return {
            r: Math.round(255 * f(0)),
            g: Math.round(255 * f(8)),
            b: Math.round(255 * f(4))
        };
    }

    rgbToHex(r, g, b) {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
    }
}
