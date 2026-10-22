    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 text-sm mt-auto py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-200">ChromaHarmonia</span>
                <span>—</span>
                <span>Gerador de Paleta de Cores Harmônicas</span>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700/80">PHP PDO</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700/80">MySQL</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700/80">Tailwind CSS</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700/80">HTML5 Canvas</span>
            </div>
            <div class="text-xs text-slate-500">
                Inspirado em <a href="https://color.adobe.com" target="_blank" rel="noopener" class="text-indigo-400 hover:underline">Adobe Color</a> &bull; Auditoria Completa
            </div>
        </div>
    </footer>

    <!-- Toast Notifications & Modals Container -->
    <?php require_once __DIR__ . '/toast.php'; ?>
    <?php require_once __DIR__ . '/modal.php'; ?>

    <!-- Global App Scripts -->
    <script src="assets/js/app.js"></script>
</body>
</html>
