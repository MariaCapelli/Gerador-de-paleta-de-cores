/**
 * Sistema Global de Aplicação (Toasts, Modais e Helpers)
 * Sistema: Gerador de Paleta de Cores
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializa auto-dismiss dos toasts existentes
    initExistingToasts();

    // 2. Toggle do menu mobile
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

/**
 * Auto-dismiss de Toasts carregados via PHP
 */
function initExistingToasts() {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toasts = container.querySelectorAll('.toast-item');
    toasts.forEach(toast => {
        const duration = parseInt(toast.getAttribute('data-auto-dismiss') || '5000', 10);
        setTimeout(() => {
            removeToastElement(toast);
        }, duration);
    });
}

function removeToastElement(toast) {
    if (!toast) return;
    toast.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => {
        toast.remove();
    }, 300);
}

/**
 * Dispara uma notificação Toast na tela
 * Tipos: 'sucesso', 'erro', 'aviso', 'info'
 */
window.showToast = function(message, type = 'sucesso', duration = 4000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast-item toast-enter pointer-events-auto flex items-start p-4 rounded-xl shadow-2xl border backdrop-blur-md transition-all duration-300 ease-out`;

    let colorClasses = '';
    let iconSvg = '';

    if (type === 'sucesso') {
        colorClasses = 'bg-emerald-950/90 border-emerald-500/50 text-emerald-100';
        iconSvg = `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
    } else if (type === 'erro') {
        colorClasses = 'bg-rose-950/90 border-rose-500/50 text-rose-100';
        iconSvg = `<svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
    } else if (type === 'aviso') {
        colorClasses = 'bg-amber-950/90 border-amber-500/50 text-amber-100';
        iconSvg = `<svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
    } else {
        colorClasses = 'bg-sky-950/90 border-sky-500/50 text-sky-100';
        iconSvg = `<svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
    }

    toast.className += ` ${colorClasses}`;

    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">${iconSvg}</div>
        <div class="flex-1 text-sm font-medium leading-relaxed">${escapeHtml(message)}</div>
        <button type="button" class="ml-3 text-slate-400 hover:text-white transition-colors" onclick="this.closest('.toast-item').remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        removeToastElement(toast);
    }, duration);
};

/**
 * Copia texto para a área de transferência com feedback visual Toast
 */
window.copyToClipboard = function(text, label = 'Código') {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            window.showToast(`${label} copiado: ${text}`, 'sucesso', 2500);
        }).catch(() => {
            fallbackCopyText(text, label);
        });
    } else {
        fallbackCopyText(text, label);
    }
};

function fallbackCopyText(text, label) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        window.showToast(`${label} copiado: ${text}`, 'sucesso', 2500);
    } catch (err) {
        window.showToast('Não foi possível copiar o texto.', 'erro');
    }
    document.body.removeChild(textArea);
}

/**
 * Modal Universal de Confirmação (Substitui confirm())
 */
window.confirmModal = function({
    title = 'Confirmação',
    message = 'Deseja realmente prosseguir?',
    confirmText = 'Confirmar',
    cancelText = 'Cancelar',
    type = 'danger', // 'danger', 'info', 'warning'
    onConfirm = () => {},
    onCancel = () => {}
}) {
    const modal = document.getElementById('universalModal');
    const box = document.getElementById('universalModalBox');
    const titleEl = document.getElementById('universalModalTitle');
    const msgEl = document.getElementById('universalModalMessage');
    const confirmBtn = document.getElementById('universalModalConfirmBtn');
    const cancelBtn = document.getElementById('universalModalCancelBtn');
    const iconContainer = document.getElementById('universalModalIconContainer');

    if (!modal) return;

    titleEl.textContent = title;
    msgEl.textContent = message;
    confirmBtn.textContent = confirmText;
    cancelBtn.textContent = cancelText;

    if (type === 'danger') {
        iconContainer.className = 'flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center bg-rose-500/20 text-rose-400';
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-lg shadow-rose-600/30 transition';
    } else if (type === 'warning') {
        iconContainer.className = 'flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center bg-amber-500/20 text-amber-400';
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-500 rounded-xl shadow-lg shadow-amber-600/30 transition';
    } else {
        iconContainer.className = 'flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center bg-indigo-500/20 text-indigo-400';
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 transition';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);

    const cleanup = () => {
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 200);
    };

    confirmBtn.onclick = () => {
        cleanup();
        onConfirm();
    };

    cancelBtn.onclick = () => {
        cleanup();
        onCancel();
    };
};

/**
 * Modal Universal de Alerta (Substitui alert())
 */
window.alertModal = function(message, title = 'Aviso') {
    return new Promise((resolve) => {
        window.confirmModal({
            title: title,
            message: message,
            confirmText: 'OK',
            cancelText: '',
            type: 'info',
            onConfirm: resolve,
            onCancel: resolve
        });
        // Esconde botão cancelar para alert simples
        const cancelBtn = document.getElementById('universalModalCancelBtn');
        if (cancelBtn) cancelBtn.style.display = 'none';
    });
};

/**
 * Confirmação de Logout customizada via Modal (sem window.confirm)
 */
window.confirmarLogout = function(event) {
    event.preventDefault();
    const href = event.currentTarget.getAttribute('href');
    window.confirmModal({
        title: 'Sair da Conta',
        message: 'Tem certeza que deseja encerrar sua sessão atual?',
        confirmText: 'Sair',
        type: 'danger',
        onConfirm: () => {
            window.location.href = href;
        }
    });
    return false;
};

/**
 * Utilitário de escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
