import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('menu-toggle');
    const closeBtn = document.getElementById('menu-close');
    const menu = document.getElementById('mobile-menu');
    const backdrop = document.getElementById('menu-backdrop');

    const openMenu = () => {
        menu.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        toggleBtn.setAttribute('aria-expanded', 'true');
    };

    const closeMenu = () => {
        menu.classList.add('hidden');
        backdrop.classList.add('hidden');
        toggleBtn.setAttribute('aria-expanded', 'false');
    };

    toggleBtn.addEventListener('click', openMenu);
    closeBtn?.addEventListener('click', closeMenu);
    backdrop.addEventListener('click', closeMenu);

    // Escape key closes menu
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMenu();
    });
});
