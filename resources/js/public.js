import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Scroll Reveal Logic
document.addEventListener('DOMContentLoaded', () => {
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
