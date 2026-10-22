<?php
/**
 * Página: Minhas Paletas e Galeria
 * Sistema: Gerador de Paleta de Cores
 * 
 * Permite visualizar, filtrar, pesquisar, editar e excluir paletas salvas
 */

$pageTitle = 'Paletas Salvas';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/services/PaletteService.php';
require_once __DIR__ . '/services/ColorService.php';

$usuarioAtual = AuthService::getUsuarioLogado();
$filtroHarmonia = $_GET['harmonia'] ?? '';
$busca = $_GET['busca'] ?? '';

$filtros = [];
if (!empty($filtroHarmonia)) $filtros['harmonia'] = $filtroHarmonia;
if (!empty($busca)) $filtros['busca'] = $busca;

$paletas = PaletteService::listar($pdo, $filtros);
$harmonias = ColorService::getHarmoniasDisponiveis();
?>

<div class="space-y-8">
    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Galeria de Paletas</span>
                <span class="text-xs font-normal text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-full border border-indigo-800/60 font-mono">
                    <?= count($paletas) ?> <?= count($paletas) === 1 ? 'Paleta' : 'Paletas' ?>
                </span>
            </h1>
            <p class="mt-1 text-sm text-slate-400">
                Visualize suas paletas salvas, copie códigos, edite metadados ou exporte para seus projetos.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="index.php" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Criar Nova Paleta</span>
            </a>
        </div>
    </div>

    <!-- Barra de Filtros e Pesquisa -->
    <form method="GET" action="paletas.php" class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="w-full md:w-80 relative">
            <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome ou descrição..." class="w-full bg-slate-800 border border-slate-700 rounded-xl pl-9 pr-4 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <select name="harmonia" class="bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas as Harmonias</option>
                <?php foreach ($harmonias as $k => $info): ?>
                    <option value="<?= $k ?>" <?= $filtroHarmonia === $k ? 'selected' : '' ?>><?= htmlspecialchars($info['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-xl border border-slate-700 transition">
                Filtrar
            </button>

            <?php if (!empty($busca) || !empty($filtroHarmonia)): ?>
                <a href="paletas.php" class="px-3 py-2 text-xs text-rose-400 hover:text-rose-300 transition">
                    Limpar filtros
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Grid de Paletas Salvas -->
    <?php if (empty($paletas)): ?>
        <div class="text-center py-16 bg-slate-900/30 rounded-3xl border border-dashed border-slate-800">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-300">Nenhuma paleta encontrada</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Crie e salve sua primeira paleta usando o círculo cromático!</p>
            <div class="mt-5">
                <a href="index.php" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition">
                    <span>Ir para o Gerador</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($paletas as $p): ?>
                <div class="bg-slate-900 rounded-3xl border border-slate-800/80 overflow-hidden shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                    <div>
                        <!-- Faixa Colorida da Paleta -->
                        <div class="h-28 w-full flex overflow-hidden cursor-pointer group" onclick="visualizarPaleta(<?= $p['id'] ?>)">
                            <?php foreach ($p['cores_array'] as $hex): ?>
                                <div class="flex-1 h-full relative group-hover:opacity-95 transition" style="background-color: <?= htmlspecialchars($hex) ?>;" title="<?= htmlspecialchars($hex) ?>"></div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Informações da Paleta -->
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-base text-slate-100 hover:text-indigo-400 transition cursor-pointer" onclick="visualizarPaleta(<?= $p['id'] ?>)">
                                    <?= htmlspecialchars($p['nome']) ?>
                                </h3>
                                <span class="text-[11px] font-mono px-2 py-0.5 rounded-full bg-slate-800 text-indigo-400 border border-slate-700/80 whitespace-nowrap">
                                    <?= htmlspecialchars($harmonias[$p['harmonia']]['nome'] ?? ucfirst($p['harmonia'])) ?>
                                </span>
                            </div>

                            <?php if (!empty($p['descricao'])): ?>
                                <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                                    <?= htmlspecialchars($p['descricao']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Badges de Códigos HEX -->
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                <?php foreach ($p['cores_array'] as $hex): ?>
                                    <button type="button" onclick="window.copyToClipboard('<?= $hex ?>', 'HEX')" title="Copiar <?= $hex ?>" class="px-2 py-1 bg-slate-950/80 hover:bg-slate-800 rounded-md font-mono text-[10px] text-slate-300 border border-slate-800 hover:border-slate-700 flex items-center space-x-1 transition">
                                        <span class="w-2 h-2 rounded-full inline-block" style="background-color: <?= $hex ?>;"></span>
                                        <span><?= $hex ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <!-- Meta: Autor e Data -->
                            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                                <span>Por <?= htmlspecialchars($p['autor_nome'] ?? 'Usuário') ?></span>
                                <span><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé com Ações -->
                    <div class="px-5 py-3 bg-slate-950/50 border-t border-slate-800 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-1">
                            <button type="button" onclick="visualizarPaleta(<?= $p['id'] ?>)" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition" title="Visualizar detalhes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <a href="api/export.php?id=<?= $p['id'] ?>&formato=css" class="p-1.5 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition" title="Baixar CSS">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            <button type="button" onclick="copiarTodasCores('<?= implode(', ', $p['cores_array']) ?>')" class="p-1.5 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition" title="Copiar todas as cores">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </div>

                        <div class="flex items-center space-x-1">
                            <button type="button" onclick="abrirModalEdicao(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nome'])) ?>', '<?= htmlspecialchars(addslashes($p['descricao'] ?? '')) ?>', '<?= $p['harmonia'] ?>', '<?= $p['cor_base'] ?>', '<?= implode(',', $p['cores_array']) ?>')" class="p-1.5 text-slate-400 hover:text-amber-400 rounded-lg hover:bg-slate-800 transition" title="Editar Paleta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" onclick="confirmarExclusao(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nome'])) ?>')" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 transition" title="Excluir Paleta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Edição de Paleta -->
<div id="editPaletteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="editPaletteModalBox">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h3 class="text-lg font-bold text-slate-100 flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span>Editar Paleta de Cores</span>
            </h3>
            <button type="button" onclick="fecharModalEdicao()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="actions/editar_paleta.php" method="POST" class="mt-4 space-y-4">
            <input type="hidden" name="id" id="editPaletteId">
            <input type="hidden" name="harmonia" id="editPaletteHarmonia">
            <input type="hidden" name="cor_base" id="editPaletteCorBase">
            <input type="hidden" name="cores" id="editPaletteCores">

            <div>
                <label for="editName" class="block text-sm font-medium text-slate-300 mb-1">Nome da Paleta *</label>
                <input type="text" id="editName" name="nome" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="editDescription" class="block text-sm font-medium text-slate-300 mb-1">Descrição</label>
                <textarea id="editDescription" name="descricao" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-3">
                <button type="button" onclick="fecharModalEdicao()" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition border border-slate-700">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 rounded-xl shadow-lg shadow-amber-600/30 transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Visualização Detalhada da Paleta -->
<div id="viewPaletteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700/80 p-6 text-left align-middle shadow-2xl transition-all scale-95 opacity-0 duration-200" id="viewPaletteModalBox">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h3 id="viewPaletteTitle" class="text-lg font-bold text-slate-100">Detalhes da Paleta</h3>
                <p id="viewPaletteSubtitle" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="fecharModalVisualizacao()" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="viewPaletteCards" class="grid grid-cols-1 sm:grid-cols-5 gap-3 my-5">
            <!-- Inserido dinamicamente via JS -->
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
            <span id="viewPaletteDesc" class="text-xs text-slate-400 italic"></span>
            <button type="button" onclick="fecharModalVisualizacao()" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition">
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
function copiarTodasCores(cores) {
    window.copyToClipboard(cores, 'Todas as cores');
}

function confirmarExclusao(id, nome) {
    window.confirmModal({
        title: 'Excluir Paleta',
        message: `Tem certeza que deseja excluir permanentemente a paleta "${nome}"? Esta ação será registrada no histórico de auditoria.`,
        confirmText: 'Excluir',
        type: 'danger',
        onConfirm: () => {
            window.location.href = `actions/excluir_paleta.php?id=${id}`;
        }
    });
}

function abrirModalEdicao(id, nome, descricao, harmonia, corBase, cores) {
    document.getElementById('editPaletteId').value = id;
    document.getElementById('editName').value = nome;
    document.getElementById('editDescription').value = descricao;
    document.getElementById('editPaletteHarmonia').value = harmonia;
    document.getElementById('editPaletteCorBase').value = corBase;
    document.getElementById('editPaletteCores').value = cores;

    const modal = document.getElementById('editPaletteModal');
    const box = document.getElementById('editPaletteModalBox');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function fecharModalEdicao() {
    const modal = document.getElementById('editPaletteModal');
    const box = document.getElementById('editPaletteModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
}

function visualizarPaleta(id) {
    fetch(`api/palette_info.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.sucesso) {
                window.showToast(data.mensagem || 'Erro ao carregar detalhes.', 'erro');
                return;
            }
            const p = data.paleta;
            document.getElementById('viewPaletteTitle').textContent = p.nome;
            document.getElementById('viewPaletteSubtitle').textContent = `Harmonia: ${p.harmonia} • Criada por ${p.autor_nome}`;
            document.getElementById('viewPaletteDesc').textContent = p.descricao || 'Sem descrição cadastrada.';

            const container = document.getElementById('viewPaletteCards');
            container.innerHTML = '';
            p.cores_detalhadas.forEach(c => {
                const card = document.createElement('div');
                card.className = 'rounded-xl overflow-hidden border border-slate-700 bg-slate-950 flex flex-col';
                card.innerHTML = `
                    <div class="h-20 w-full flex items-center justify-center font-mono text-xs font-bold" style="background-color: ${c.hex}; color: ${c.textColor};">
                        ${c.hex}
                    </div>
                    <div class="p-2 space-y-1 text-[10px]">
                        <button type="button" onclick="window.copyToClipboard('${c.hex}', 'HEX')" class="w-full text-left font-mono text-slate-300 hover:text-indigo-400 truncate">
                            ${c.hex}
                        </button>
                        <button type="button" onclick="window.copyToClipboard('${c.rgb}', 'RGB')" class="w-full text-left font-mono text-slate-400 hover:text-indigo-400 truncate">
                            ${c.rgb}
                        </button>
                        <button type="button" onclick="window.copyToClipboard('${c.hsl}', 'HSL')" class="w-full text-left font-mono text-slate-400 hover:text-indigo-400 truncate">
                            ${c.hsl}
                        </button>
                    </div>
                `;
                container.appendChild(card);
            });

            const modal = document.getElementById('viewPaletteModal');
            const box = document.getElementById('viewPaletteModalBox');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        });
}

function fecharModalVisualizacao() {
    const modal = document.getElementById('viewPaletteModal');
    const box = document.getElementById('viewPaletteModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
