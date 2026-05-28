import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

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
