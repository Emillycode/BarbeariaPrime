(function () {
    // 1. Aplica o tema imediatamente para evitar tela piscando (FOUC)
    const temaSalvo = localStorage.getItem('barbearia_tema');
    const temaInicial = temaSalvo || 'dark';
    document.documentElement.setAttribute('data-theme', temaInicial);
})();

document.addEventListener('DOMContentLoaded', function () {
    const botoesTema = document.querySelectorAll('.theme-toggle');

    const iconeSol = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
    `;

    const iconeLua = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    `;

    function atualizarIcones(temaAtual) {
        botoesTema.forEach(btn => {
            if (temaAtual === 'light') {
                btn.innerHTML = iconeLua + '<span class="theme-label">Modo escuro</span>';
                btn.setAttribute('aria-label', 'Mudar para modo escuro');
                btn.setAttribute('title', 'Mudar para modo escuro');
            } else {
                btn.innerHTML = iconeSol + '<span class="theme-label">Modo claro</span>';
                btn.setAttribute('aria-label', 'Mudar para modo claro');
                btn.setAttribute('title', 'Mudar para modo claro');
            }
        });
    }

    const temaAtual = document.documentElement.getAttribute('data-theme') || 'dark';
    atualizarIcones(temaAtual);

    botoesTema.forEach(btn => {
        btn.addEventListener('click', function () {
            const temaAgora = document.documentElement.getAttribute('data-theme') || 'dark';
            const novoTema = temaAgora === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', novoTema);
            localStorage.setItem('barbearia_tema', novoTema);
            atualizarIcones(novoTema);
        });
    });
});
