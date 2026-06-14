import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

function improveAccessibility(root = document) {
    let generatedId = 0;

    root.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(control => {
        if (control.getAttribute('aria-label') || control.getAttribute('aria-labelledby') || control.closest('label')) {
            return;
        }

        const container = control.parentElement;
        const label = container?.querySelector('label:not([for])');
        if (label) {
            const baseName = control.name?.replace(/\W+/g, '-') || 'control';
            control.id ||= `field-${baseName}-${++generatedId}`;
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
        };
        const iconName = Object.keys(iconNames).find(name => icon?.classList.contains(name));
        button.setAttribute('aria-label', button.title || iconNames[iconName] || 'Tombol aksi');
    });
}

// Scroll Reveal Logic
document.addEventListener('DOMContentLoaded', () => {
    improveAccessibility();

    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => observer.observe(el));
});

// Image Skeleton Handler
window.addEventListener('load', () => {
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        if (!img.complete) {
            const wrapper = img.parentElement;
            if (wrapper) wrapper.classList.add('skeleton');
            img.addEventListener('load', () => wrapper.classList.remove('skeleton'));
        }
    });
});
