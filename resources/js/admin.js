import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

function improveAccessibility(root = document) {
    let generatedId = 0;

    root.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(control => {
        if (control.getAttribute('aria-label') || control.getAttribute('aria-labelledby') || control.closest('label')) {
            return;
        }

        const label = control.parentElement?.querySelector('label:not([for])');
        if (label) {
            const baseName = control.name?.replace(/\W+/g, '-') || 'control';
            control.id ||= `admin-field-${baseName}-${++generatedId}`;
            label.htmlFor = control.id;
            return;
        }

        const accessibleName = control.title || control.placeholder || control.name;
        if (accessibleName) {
            control.setAttribute('aria-label', accessibleName.replaceAll('_', ' '));
        }
    });

    root.querySelectorAll('button').forEach(button => {
        if (button.getAttribute('aria-label') || button.textContent.trim()) {
            return;
        }

        const icon = button.querySelector('i');
        const iconNames = {
            'fa-bars': 'Buka menu',
            'fa-times': 'Tutup',
            'fa-trash': 'Hapus',
            'fa-edit': 'Edit',
            'fa-search': 'Cari',
            'fa-clipboard-check': 'Verifikasi',
            'fa-exclamation': 'Tandai berkas kurang',
        };
        const iconName = Object.keys(iconNames).find(name => icon?.classList.contains(name));
        button.setAttribute('aria-label', button.title || iconNames[iconName] || 'Tombol aksi');
    });
}

// Toast management logic (extracted from layouts/admin)
document.addEventListener('alpine:init', () => {
    if (!Alpine.store('toasts')) {
        Alpine.store('toasts', []);
    }
    window.toast = (message, type = 'success') => {
        Alpine.store('toasts').push({ message, type, id: Date.now() });
    };
});

document.addEventListener('DOMContentLoaded', function() {
    improveAccessibility();

    const body = document.body;
    
    function getAlpineData() {
        if (body._x_dataStack && body._x_dataStack.length > 0) return body._x_dataStack[0];
        return null;
    }

    // Confirmation Modal Handler
    document.querySelectorAll('form[data-confirm]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const alpineComponent = getAlpineData();
            const message = form.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
            const title = form.getAttribute('data-title') || 'Konfirmasi Hapus';
            const button = form.getAttribute('data-button') || 'Hapus';
            const type = form.getAttribute('data-type') || 'danger';
            const icon = form.getAttribute('data-icon') || 'fa-trash';
            
            if (alpineComponent && typeof alpineComponent.deleteModal !== 'undefined') {
                alpineComponent.deleteMessage = message;
                alpineComponent.deleteTitle = title;
                alpineComponent.deleteConfirmText = button;
                alpineComponent.deleteType = type;
                alpineComponent.deleteIcon = icon;
                alpineComponent.deleteForm = form;
                alpineComponent.deleteModal = true;
            } else {
                if (confirm(message)) form.submit();
            }
        });
    });
});
