const menuToggle = document.querySelector('[data-nav-toggle]');
const mainNav = document.querySelector('#main-nav');

if (menuToggle instanceof HTMLButtonElement && mainNav instanceof HTMLElement) {
    const closeMenu = () => {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Buka menu navigasi');
        mainNav.classList.remove('is-open');
    };

    menuToggle.addEventListener('click', () => {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isExpanded));
        menuToggle.setAttribute('aria-label', isExpanded ? 'Buka menu navigasi' : 'Tutup menu navigasi');
        mainNav.classList.toggle('is-open', !isExpanded);
    });

    mainNav.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuToggle.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            menuToggle.focus();
        }
    });

    window.matchMedia('(min-width: 701px)').addEventListener('change', (event) => {
        if (event.matches) {
            closeMenu();
        }
    });
}
