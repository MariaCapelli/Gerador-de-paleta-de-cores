<?php
/**
 * Header Global do Sistema
 * Sistema: Gerador de Paleta de Cores
 */

require_once __DIR__ . '/../bd.php';
require_once __DIR__ . '/../services/AuthService.php';

$usuarioAtual = AuthService::getUsuarioLogado();
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Gerador de Paleta de Cores Harmônicas</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        accent: {
                            500: '#6366f1',
                            600: '#4f46e5',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'monospace']
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="flex flex-col min-h-screen bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white">

    <!-- Top Notification Bar / Banner -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <a href="index.php" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 4 4 0 014-4c2 0 2 2 4 2 1.5 0 2.5-.5 3.5-1.5 1-1 1.5-2 1.5-3.5 0-2-2-4-2-4a4 4 0 014-4 4 4 0 014 4c0 3-1 6-4 9-2 2-4 3-7 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                                ChromaHarmonia
                            </span>
                            <span class="hidden sm:inline-block text-xs text-indigo-400 font-mono ml-1.5 px-1.5 py-0.5 rounded bg-indigo-950/60 border border-indigo-800/50">
                                Adobe Color Style
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="index.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $paginaAtual === 'index.php' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Círculo Cromático</span>
                        </span>
                    </a>
                    <a href="extrair_imagem.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $paginaAtual === 'extrair_imagem.php' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Extrair de Imagem</span>
                        </span>
                    </a>
                    <a href="paletas.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $paginaAtual === 'paletas.php' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Minhas Paletas</span>
                        </span>
                    </a>
                    <a href="historico.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $paginaAtual === 'historico.php' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Histórico & Auditoria</span>
                        </span>
                    </a>
                </nav>

                <!-- User Session / Controls -->
                <div class="flex items-center space-x-3">
                    <?php if ($usuarioAtual): ?>
                        <div class="flex items-center space-x-3 bg-slate-800/80 border border-slate-700/60 py-1.5 px-3 rounded-full">
                            <div class="w-7 h-7 rounded-full bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center text-xs ring-1 ring-indigo-500/40">
                                <?= strtoupper(substr($usuarioAtual['nome'], 0, 1)) ?>
                            </div>
                            <span class="hidden sm:inline text-xs font-medium text-slate-200">
                                <?= htmlspecialchars(explode(' ', $usuarioAtual['nome'])[0]) ?>
                            </span>
                            <a href="actions/auth.php?acao=logout" title="Sair da conta" class="text-slate-400 hover:text-rose-400 transition-colors p-1" onclick="return confirmarLogout(event)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-2">
                            <a href="actions/auth.php?acao=demo" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium px-3 py-1.5 rounded-lg border border-slate-700 transition">
                                Demo Rápido
                            </a>
                            <a href="login.php" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-3.5 py-1.5 rounded-lg shadow-sm shadow-indigo-600/30 transition">
                                Entrar
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Hamburger Button -->
                    <button id="mobileMenuBtn" type="button" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Nav Collapsible -->
            <div id="mobileMenu" class="hidden md:hidden pb-4 pt-2 border-t border-slate-800/80 space-y-1">
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $paginaAtual === 'index.php' ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                    🎨 Círculo Cromático
                </a>
                <a href="extrair_imagem.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $paginaAtual === 'extrair_imagem.php' ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                    🖼️ Extrair de Imagem
                </a>
                <a href="paletas.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $paginaAtual === 'paletas.php' ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                    📂 Minhas Paletas
                </a>
                <a href="historico.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $paginaAtual === 'historico.php' ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                    📜 Histórico & Auditoria
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
