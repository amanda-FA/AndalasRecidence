const landingPage = document.querySelector('[data-landing-page]');

if (landingPage) {
    const header = document.getElementById('landing-header');
    const menu = document.getElementById('landing-menu-toggle');
    const navigation = document.getElementById('landing-navigation');
    const updateHeader = () => header.classList.toggle('is-scrolled', header.dataset.hero !== 'true' || window.scrollY > 24);
    const closeMenu = () => {
        header.classList.remove('menu-open');
        menu.setAttribute('aria-expanded', 'false');
        menu.setAttribute('aria-label', 'Buka menu navigasi');
        menu.querySelector('span').textContent = 'menu';
    };
    menu.addEventListener('click', () => {
        const open = menu.getAttribute('aria-expanded') !== 'true';
        header.classList.toggle('menu-open', open);
        menu.setAttribute('aria-expanded', String(open));
        menu.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
        menu.querySelector('span').textContent = open ? 'close' : 'menu';
    });
    navigation.addEventListener('click', event => { if (event.target.closest('a')) closeMenu(); });
    document.addEventListener('click', event => { if (!header.contains(event.target)) closeMenu(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && menu.getAttribute('aria-expanded') === 'true') { closeMenu(); menu.focus(); } });
    window.matchMedia('(min-width: 1100px)').addEventListener('change', event => { if (event.matches) closeMenu(); });
    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();
    if (header.dataset.hero === 'true' && 'IntersectionObserver' in window) {
        const links = [...navigation.querySelectorAll('[data-section]')];
        const observer = new IntersectionObserver(entries => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;
                links.forEach(link => {
                    if (link.dataset.section === entry.target.id) link.setAttribute('aria-current', 'location');
                    else link.removeAttribute('aria-current');
                });
            }
        }, { rootMargin: '-15% 0px -50% 0px' });
        links.forEach(link => { const section = document.getElementById(link.dataset.section); if (section) observer.observe(section); });
    }
}
