<?php
/**
 * Exportador de Paletas
 * Sistema: Gerador de Paleta de Cores
 * 
 * Formatos suportados: css, json, tailwind, svg
 */

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/PaletteService.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$formato = strtolower($_GET['formato'] ?? 'css');

// Pode vir de uma paleta salva por ID ou por parâmetros na query string
if ($id > 0) {
    $paleta = PaletteService::buscarPorId($pdo, $id);
    if (!$paleta) {
        die("Paleta não encontrada.");
    }
    $nome = preg_replace('/[^a-zA-Z0-9_-]/', '_', $paleta['nome']);
    $cores = $paleta['cores_array'];
} else {
    $nome = 'paleta_gerada';
    $coresRaw = $_GET['cores'] ?? '#FF5733,#33FF57,#3357FF,#F333FF,#33FFF3';
    $cores = array_filter(explode(',', $coresRaw));
}

switch ($formato) {
    case 'json':
        header('Content-Type: application/json; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nome}.json\"");
        echo json_encode([
            'nome' => $nome,
            'cores' => $cores
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;

    case 'tailwind':
        header('Content-Type: text/plain; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nome}-tailwind.js\"");
        echo "// Adicione no seu tailwind.config.js dentro de theme.extend.colors:\n";
        echo "colors: {\n";
        echo "  '{$nome}': {\n";
        foreach ($cores as $idx => $cor) {
            $num = ($idx + 1) * 100;
            echo "    '{$num}': '{$cor}',\n";
        }
        echo "  }\n";
        echo "}\n";
        exit;

    case 'svg':
        header('Content-Type: image/svg+xml');
        header("Content-Disposition: attachment; filename=\"{$nome}.svg\"");
        $totalCores = count($cores);
        $larguraCard = 120;
        $alturaCard = 160;
        $larguraTotal = $totalCores * $larguraCard;
        
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<svg width=\"{$larguraTotal}\" height=\"{$alturaCard}\" viewBox=\"0 0 {$larguraTotal} {$alturaCard}\" xmlns=\"http://www.w3.org/2000/svg\">\n";
        echo "  <style>\n";
        echo "    .label { font-family: monospace; font-size: 14px; font-weight: bold; fill: #FFFFFF; }\n";
        echo "  </style>\n";
        foreach ($cores as $idx => $cor) {
            $x = $idx * $larguraCard;
            $textColor = ColorService::getCorTextoParaContraste($cor);
            echo "  <g transform=\"translate({$x}, 0)\">\n";
            echo "    <rect width=\"{$larguraCard}\" height=\"{$alturaCard}\" fill=\"{$cor}\" />\n";
            echo "    <text x=\"60\" y=\"130\" text-anchor=\"middle\" class=\"label\" fill=\"{$textColor}\">{$cor}</text>\n";
            echo "  </g>\n";
        }
        echo "</svg>\n";
        exit;

    case 'css':
    default:
        header('Content-Type: text/css; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nome}.css\"");
        echo "/* Paleta: {$nome} */\n";
        echo ":root {\n";
        foreach ($cores as $idx => $cor) {
            $num = $idx + 1;
            echo "  --cor-{$num}: {$cor};\n";
        }
        echo "}\n";
        exit;
}
