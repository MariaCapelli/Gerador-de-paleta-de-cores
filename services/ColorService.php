<?php
/**
 * Serviço de Cores e Harmonias Cromáticas
 * Sistema: Gerador de Paleta de Cores
 * 
 * Lógica matemática isolada da interface para conversões HEX / RGB / HSL
 * e geração de harmonias cromáticas baseadas no círculo cromático.
 */

class ColorService {

    /**
     * Valida se uma string é um código HEX válido (ex: #ffffff ou ffffff)
     */
    public static function sanitizarHex(string $hex): string {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('/^[a-fA-F0-9]{6}$/', $hex)) {
            return '#3B82F6'; // Cor padrão (Azul moderno) em caso de valor inválido
        }
        return '#' . strtoupper($hex);
    }

    /**
     * Converte código HEX (#RRGGBB) para array RGB [r, g, b] (0-255)
     */
    public static function hexParaRgb(string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Converte array RGB [r, g, b] para código HEX (#RRGGBB)
     */
    public static function rgbParaHex(int $r, int $g, int $b): string {
        $r = max(0, min(255, $r));
        $g = max(0, min(255, $g));
        $b = max(0, min(255, $b));
        return sprintf("#%02X%02X%02X", $r, $g, $b);
    }

    /**
     * Converte RGB (0-255) para HSL [h: 0-360, s: 0-100, l: 0-100]
     */
    public static function rgbParaHsl(int $r, int $g, int $b): array {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $diff = $max - $min;

        $l = ($max + $min) / 2;

        if ($diff == 0) {
            $h = 0;
            $s = 0;
        } else {
            $s = ($l > 0.5) ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $diff + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $diff + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $diff + 4;
                    break;
                default:
                    $h = 0;
            }
            $h /= 6;
        }

        return [
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100)
        ];
    }

    /**
     * Converte HSL [h: 0-360, s: 0-100, l: 0-100] para RGB [r, g, b]
     */
    public static function hslParaRgb(float $h, float $s, float $l): array {
        $h = fmod($h, 360);
        if ($h < 0) {
            $h += 360;
        }

        $s = max(0, min(100, $s)) / 100;
        $l = max(0, min(100, $l)) / 100;

        if ($s == 0) {
            $r = $g = $b = round($l * 255);
            return ['r' => $r, 'g' => $g, 'b' => $b];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $hNorm = $h / 360;

        $tr = $hNorm + 1 / 3;
        $tg = $hNorm;
        $tb = $hNorm - 1 / 3;

        return [
            'r' => round(self::hueParaRgb($p, $q, $tr) * 255),
            'g' => round(self::hueParaRgb($p, $q, $tg) * 255),
            'b' => round(self::hueParaRgb($p, $q, $tb) * 255)
        ];
    }

    private static function hueParaRgb(float $p, float $q, float $t): float {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

    /**
     * Calcula a luminância relativa (WCAG) para contraste de texto
     */
    public static function calcularLuminancia(int $r, int $g, int $b): float {
        $valores = array_map(function($v) {
            $v = $v / 255;
            return ($v <= 0.03928) ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        }, [$r, $g, $b]);

        return 0.2126 * $valores[0] + 0.7152 * $valores[1] + 0.0722 * $valores[2];
    }

    /**
     * Retorna a melhor cor para contraste de texto sobre um fundo (preto ou branco)
     */
    public static function getCorTextoParaContraste(string $hex): string {
        $rgb = self::hexParaRgb($hex);
        $lum = self::calcularLuminancia($rgb['r'], $rgb['g'], $rgb['b']);
        return ($lum > 0.4) ? '#0F172A' : '#FFFFFF';
    }

    /**
     * Monta o objeto descritor completo de uma cor (HEX, RGB, HSL e Contraste)
     */
    public static function formatarDescritorCor(string $hex, bool $isBase = false): array {
        $hex = self::sanitizarHex($hex);
        $rgb = self::hexParaRgb($hex);
        $hsl = self::rgbParaHsl($rgb['r'], $rgb['g'], $rgb['b']);
        $textColor = self::getCorTextoParaContraste($hex);

        return [
            'hex' => $hex,
            'rgb' => "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})",
            'rgbValues' => $rgb,
            'hsl' => "hsl({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)",
            'hslValues' => $hsl,
            'textColor' => $textColor,
            'isBase' => $isBase
        ];
    }

    /**
     * Gera uma paleta de 5 cores com base na cor base e no tipo de harmonia
     * 
     * Harmonias suportadas:
     * - monocromatica
     * - analoga
     * - homologa
     * - complementar
     * - complementar_dividida
     * - triade
     * - tetrade
     */
    public static function gerarPaleta(string $corBaseHex, string $harmonia): array {
        $corBaseHex = self::sanitizarHex($corBaseHex);
        $rgb = self::hexParaRgb($corBaseHex);
        $hsl = self::rgbParaHsl($rgb['r'], $rgb['g'], $rgb['b']);

        $h = $hsl['h'];
        $s = $hsl['s'];
        $l = $hsl['l'];

        $hslCores = [];

        switch (strtolower($harmonia)) {
            case 'monocromatica':
                // Mesmo matiz com diferentes luminosidades e saturações
                $hslCores = [
                    ['h' => $h, 's' => max(15, $s * 0.4), 'l' => min(94, $l + 32)],
                    ['h' => $h, 's' => max(25, $s * 0.7), 'l' => min(82, $l + 16)],
                    ['h' => $h, 's' => $s,                'l' => $l, 'isBase' => true],
                    ['h' => $h, 's' => min(100, $s * 1.1),'l' => max(18, $l - 18)],
                    ['h' => $h, 's' => min(100, $s * 1.2),'l' => max(10, $l - 34)]
                ];
                break;

            case 'analoga':
                // Matizes vizinhos no círculo cromático (-30°, -15°, 0°, +15°, +30°)
                $hslCores = [
                    ['h' => ($h - 30 + 360) % 360, 's' => max(20, $s * 0.9), 'l' => min(85, $l + 6)],
                    ['h' => ($h - 15 + 360) % 360, 's' => $s,                'l' => min(80, $l + 3)],
                    ['h' => $h,                    's' => $s,                'l' => $l, 'isBase' => true],
                    ['h' => ($h + 15) % 360,       's' => $s,                'l' => max(20, $l - 4)],
                    ['h' => ($h + 30) % 360,       's' => max(20, $s * 0.9), 'l' => max(15, $l - 8)]
                ];
                break;

            case 'homologa':
                // Harmonização homogênea/homóloga (matizes muito próximos com variação sutil de brilho)
                $hslCores = [
                    ['h' => ($h - 20 + 360) % 360, 's' => max(25, $s * 0.85), 'l' => min(88, $l + 12)],
                    ['h' => ($h - 10 + 360) % 360, 's' => max(30, $s * 0.95), 'l' => min(82, $l + 6)],
                    ['h' => $h,                    's' => $s,                 'l' => $l, 'isBase' => true],
                    ['h' => ($h + 10) % 360,       's' => $s,                 'l' => max(25, $l - 6)],
                    ['h' => ($h + 20) % 360,       's' => min(100, $s * 1.05),'l' => max(18, $l - 14)]
                ];
                break;

            case 'complementar':
                // Cor base e oposta a 180° com variações de luminosidade
                $compH = ($h + 180) % 360;
                $hslCores = [
                    ['h' => $h,     's' => max(20, $s * 0.75), 'l' => min(88, $l + 20)],
                    ['h' => $h,     's' => $s,                'l' => $l, 'isBase' => true],
                    ['h' => $h,     's' => min(100, $s * 1.1),'l' => max(20, $l - 20)],
                    ['h' => $compH, 's' => $s,                'l' => $l],
                    ['h' => $compH, 's' => min(100, $s * 1.1),'l' => max(22, $l - 18)]
                ];
                break;

            case 'complementar_dividida':
                // Cor base + dois ângulos adjacentes ao complemento (180° ± 30°)
                $comp1 = ($h + 150) % 360;
                $comp2 = ($h + 210) % 360;
                $hslCores = [
                    ['h' => $comp1, 's' => max(30, $s * 0.9), 'l' => min(85, $l + 8)],
                    ['h' => $h,     's' => max(20, $s * 0.7), 'l' => min(90, $l + 18)],
                    ['h' => $h,     's' => $s,                'l' => $l, 'isBase' => true],
                    ['h' => $comp2, 's' => $s,                'l' => $l],
                    ['h' => $comp2, 's' => min(100, $s * 1.1),'l' => max(20, $l - 16)]
                ];
                break;

            case 'triade':
                // Três cores equidistantes a 120° (0°, 120°, 240°) com 2 tons de transição
                $triade1 = ($h + 120) % 360;
                $triade2 = ($h + 240) % 360;
                $hslCores = [
                    ['h' => $triade2, 's' => max(30, $s * 0.85), 'l' => min(85, $l + 10)],
                    ['h' => $triade1, 's' => $s,                 'l' => $l],
                    ['h' => $h,       's' => $s,                 'l' => $l, 'isBase' => true],
                    ['h' => $triade2, 's' => $s,                 'l' => $l],
                    ['h' => $triade1, 's' => min(100, $s * 1.1), 'l' => max(20, $l - 18)]
                ];
                break;

            case 'tetrade':
            default:
                // Quatro cores a 90° (0°, 90°, 180°, 270°)
                $tet1 = ($h + 90) % 360;
                $tet2 = ($h + 180) % 360;
                $tet3 = ($h + 270) % 360;
                $hslCores = [
                    ['h' => $tet1, 's' => max(30, $s * 0.9), 'l' => min(82, $l + 8)],
                    ['h' => $tet2, 's' => $s,                'l' => $l],
                    ['h' => $h,    's' => $s,                'l' => $l, 'isBase' => true],
                    ['h' => $tet3, 's' => $s,                'l' => $l],
                    ['h' => $tet1, 's' => min(100, $s * 1.1),'l' => max(20, $l - 20)]
                ];
                break;
        }

        // Converte cada HSL gerado para o objeto completo com HEX, RGB e HSL
        $paleta = [];
        foreach ($hslCores as $item) {
            $rgbCor = self::hslParaRgb($item['h'], $item['s'], $item['l']);
            $hexCor = self::rgbParaHex($rgbCor['r'], $rgbCor['g'], $rgbCor['b']);
            $paleta[] = self::formatarDescritorCor($hexCor, !empty($item['isBase']));
        }

        return $paleta;
    }

    /**
     * Retorna a lista amigável de harmonias disponíveis
     */
    public static function getHarmoniasDisponiveis(): array {
        return [
            'analoga' => [
                'nome' => 'Análoga',
                'descricao' => 'Cores vizinhas no círculo cromático, gerando sensações suaves e elegantes.'
            ],
            'monocromatica' => [
                'nome' => 'Monocromática',
                'descricao' => 'Variações de saturação e brilho do mesmo matiz. Coesa e profissional.'
            ],
            'triade' => [
                'nome' => 'Tríade',
                'descricao' => 'Três pontos equidistantes no círculo cromático. Vibrante e equilibrada.'
            ],
            'complementar' => [
                'nome' => 'Complementar',
                'descricao' => 'Cores opostas no círculo cromático. Alto impacto e contraste vibrante.'
            ],
            'complementar_dividida' => [
                'nome' => 'Complementar Dividida',
                'descricao' => 'Combina a cor base com as vizinhas da sua complementar. Mais suave que a complementar.'
            ],
            'tetrade' => [
                'nome' => 'Tétrade (Quadrada)',
                'descricao' => 'Quatro cores distribuídas em ângulos retos. Ricas em variedade de tons.'
            ],
            'homologa' => [
                'nome' => 'Homóloga',
                'descricao' => 'Tons adjacentes com transições fluidas e harmonia sutil.'
            ]
        ];
    }
}
