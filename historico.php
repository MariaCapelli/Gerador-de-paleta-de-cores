<?php
/**
 * Página: Histórico & Auditoria
 * Sistema: Gerador de Paleta de Cores
 * 
 * Exibe o log completo de auditoria das operações de criação, edição e exclusão
 * realizadas no sistema conforme exigido nas regras de negócio.
 */

$pageTitle = 'Histórico & Auditoria';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/services/AuditService.php';

$filtroAcao = $_GET['acao'] ?? '';
$filtroUsuario = $_GET['usuario'] ?? '';
$filtroInicio = $_GET['data_inicio'] ?? '';
$filtroFim = $_GET['data_fim'] ?? '';
$filtroBusca = $_GET['busca'] ?? '';

$filtros = [];
if (!empty($filtroAcao)) $filtros['acao'] = $filtroAcao;
if (!empty($filtroUsuario)) $filtros['usuario'] = $filtroUsuario;
if (!empty($filtroInicio)) $filtros['data_inicio'] = $filtroInicio;
if (!empty($filtroFim)) $filtros['data_fim'] = $filtroFim;
if (!empty($filtroBusca)) $filtros['busca'] = $filtroBusca;

$limite = 50;
$pagina = max(1, (int)($_GET['p'] ?? 1));
$offset = ($pagina - 1) * $limite;

$registros = AuditService::listar($pdo, $filtros, $limite, $offset);
$totalRegistros = AuditService::contar($pdo, $filtros);
$totalPaginas = ceil($totalRegistros / $limite);
$stats = AuditService::obterEstatisticas($pdo);
?>

<div class="space-y-8">
    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Histórico de Auditoria</span>
                <span class="text-xs font-normal text-emerald-400 bg-emerald-950/80 px-2.5 py-1 rounded-full border border-emerald-800/60 font-mono">
                    Rastreabilidade Ativa
                </span>
            </h1>
            <p class="mt-1 text-sm text-slate-400">
                Consulta e conformidade das ações realizadas pelos usuários (criações, edições e exclusões de paletas).
            </p>
        </div>
    </div>

    <!-- Cards de Estatísticas Rápidas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total de Ações</span>
            <p class="text-2xl font-black text-slate-100 mt-1"><?= number_format($stats['total'], 0, ',', '.') ?></p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-xl">
            <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Criações</span>
            <p class="text-2xl font-black text-emerald-400 mt-1"><?= number_format($stats['criacoes'], 0, ',', '.') ?></p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-xl">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Edições</span>
            <p class="text-2xl font-black text-amber-400 mt-1"><?= number_format($stats['edicoes'], 0, ',', '.') ?></p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-xl">
            <span class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Exclusões</span>
            <p class="text-2xl font-black text-rose-400 mt-1"><?= number_format($stats['exclusoes'], 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- Barra de Filtros de Auditoria -->
    <form method="GET" action="historico.php" class="bg-slate-900/60 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Ação</label>
                <select name="acao" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todas as Ações</option>
                    <option value="Criação" <?= $filtroAcao === 'Criação' ? 'selected' : '' ?>>Criação</option>
                    <option value="Edição" <?= $filtroAcao === 'Edição' ? 'selected' : '' ?>>Edição</option>
                    <option value="Exclusão" <?= $filtroAcao === 'Exclusão' ? 'selected' : '' ?>>Exclusão</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Usuário</label>
                <input type="text" name="usuario" value="<?= htmlspecialchars($filtroUsuario) ?>" placeholder="Nome ou e-mail..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Data Início</label>
                <input type="date" name="data_inicio" value="<?= htmlspecialchars($filtroInicio) ?>" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Data Fim</label>
                <input type="date" name="data_fim" value="<?= htmlspecialchars($filtroFim) ?>" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
            <span class="text-xs text-slate-400"><?= $totalRegistros ?> eventos registrados</span>
            <div class="flex items-center space-x-2">
                <?php if (!empty($filtroAcao) || !empty($filtroUsuario) || !empty($filtroInicio) || !empty($filtroFim)): ?>
                    <a href="historico.php" class="px-3 py-1.5 text-xs text-rose-400 hover:text-rose-300 transition">
                        Limpar Filtros
                    </a>
                <?php endif; ?>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md transition">
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </form>

    <!-- Tabela de Auditoria (Desktop e Mobile Card Stack) -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-sm">
                <thead class="bg-slate-950/80 text-slate-400 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th scope="col" class="px-6 py-4">Data / Hora</th>
                        <th scope="col" class="px-6 py-4">Usuário</th>
                        <th scope="col" class="px-6 py-4">Ação</th>
                        <th scope="col" class="px-6 py-4">Registro Afetado</th>
                        <th scope="col" class="px-6 py-4">IP Origem</th>
                        <th scope="col" class="px-6 py-4 text-right">Detalhes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    <?php if (empty($registros)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Nenhum registro de auditoria encontrado com os filtros selecionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registros as $reg): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-300">
                                    <?= date('d/m/Y H:i:s', strtotime($reg['data_hora'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-200">
                                    <?= htmlspecialchars($reg['usuario_nome']) ?>
                                    <?php if (!empty($reg['usuario_email'])): ?>
                                        <span class="block text-[11px] text-slate-500 font-mono"><?= htmlspecialchars($reg['usuario_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <?php 
                                        $badgeClass = 'bg-slate-800 text-slate-300 border-slate-700';
                                        if (str_contains(mb_strtolower($reg['acao']), 'cria')) {
                                            $badgeClass = 'bg-emerald-950/80 text-emerald-400 border-emerald-800/60';
                                        } elseif (str_contains(mb_strtolower($reg['acao']), 'edi')) {
                                            $badgeClass = 'bg-amber-950/80 text-amber-400 border-amber-800/60';
                                        } elseif (str_contains(mb_strtolower($reg['acao']), 'exclu')) {
                                            $badgeClass = 'bg-rose-950/80 text-rose-400 border-rose-800/60';
                                        }
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full font-semibold text-[11px] border <?= $badgeClass ?>">
                                        <?= htmlspecialchars($reg['acao']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-200">
                                    <?= htmlspecialchars($reg['registro_afetado']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-400">
                                    <?= htmlspecialchars($reg['ip_origem'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-right">
                                    <?php if (!empty($reg['detalhes'])): ?>
                                        <button type="button" onclick="abrirDetalhesAuditoria(<?= htmlspecialchars(json_encode($reg['registro_afetado'])) ?>, <?= htmlspecialchars(json_encode($reg['detalhes'])) ?>)" class="text-indigo-400 hover:text-indigo-300 font-medium text-xs flex items-center space-x-1 ml-auto">
                                            <span>Ver JSON</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-slate-600">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <div class="px-6 py-4 bg-slate-950/70 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>
                <div class="flex items-center space-x-1.5">
                    <?php if ($pagina > 1): ?>
                        <a href="?p=<?= $pagina - 1 ?>&acao=<?= urlencode($filtroAcao) ?>&usuario=<?= urlencode($filtroUsuario) ?>" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-200 transition">Anterior</a>
                    <?php endif; ?>
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="?p=<?= $pagina + 1 ?>&acao=<?= urlencode($filtroAcao) ?>&usuario=<?= urlencode($filtroUsuario) ?>" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-slate-200 transition">Próxima</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Detalhes de Auditoria (JSON Formatado) -->
<div id="auditDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-xl transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="auditDetailsModalBox">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-100">Registro de Auditoria</h3>
                <p id="auditModalTitle" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="fecharDetalhesAuditoria()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold text-slate-400 mb-1">Conteúdo do Snapshot</label>
            <pre id="auditModalJson" class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs font-mono text-emerald-300 overflow-x-auto max-h-80"></pre>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="button" onclick="fecharDetalhesAuditoria()" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition">
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
function abrirDetalhesAuditoria(registroAfetado, detalhesJson) {
    document.getElementById('auditModalTitle').textContent = registroAfetado;
    let formatado = detalhesJson;
    try {
        const parsed = JSON.parse(detalhesJson);
        formatado = JSON.stringify(parsed, null, 2);
    } catch(e) {}
    document.getElementById('auditModalJson').textContent = formatado;

    const modal = document.getElementById('auditDetailsModal');
    const box = document.getElementById('auditDetailsModalBox');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function fecharDetalhesAuditoria() {
    const modal = document.getElementById('auditDetailsModal');
    const box = document.getElementById('auditDetailsModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
