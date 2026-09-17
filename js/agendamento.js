document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('appointmentForm');
    if (!form) return;

    const campoData = document.getElementById('data');
    const campoBarbeiro = document.getElementById('barbeiro');
    const campoHorario = document.getElementById('horario');
    const avisoIndisponivel = document.getElementById('avisoIndisponivel');
    const botaoSubmit = form.querySelector('button[type="submit"]');

    // Não permite selecionar datas passadas
    const hoje = new Date().toISOString().split('T')[0];
    campoData.setAttribute('min', hoje);

    // URL base da aplicação (detecta se está em subdiretório como /barbearia-prime/ ou na raiz)
    const rawBase = form.dataset.baseUrl || '';
    const baseUrl = rawBase.endsWith('/') ? rawBase : (rawBase ? rawBase + '/' : '');
    const apiVerificar = baseUrl ? `${baseUrl}api/verificar_disponibilidade.php` : 'api/verificar_disponibilidade.php';
    const apiAgendar = baseUrl ? `${baseUrl}api/agendar.php` : 'api/agendar.php';

    async function checarDisponibilidade() {
        const barbeiro = campoBarbeiro.value;
        const data = campoData.value;
        const horario = campoHorario.value;

        if (!barbeiro || !data || !horario) {
            avisoIndisponivel.classList.remove('show');
            botaoSubmit.disabled = false;
            return;
        }

        try {
            const resp = await fetch(`${apiVerificar}?barbeiro=${encodeURIComponent(barbeiro)}&data=${encodeURIComponent(data)}&horario=${encodeURIComponent(horario)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const json = await resp.json();

            if (json.disponivel === false) {
                avisoIndisponivel.textContent = 'Esse horário já está reservado para este barbeiro. Escolha outro horário ou profissional.';
                avisoIndisponivel.classList.add('show');
                botaoSubmit.disabled = true;
            } else {
                avisoIndisponivel.classList.remove('show');
                botaoSubmit.disabled = false;
            }
        } catch (e) {
            // Falha de rede não deve travar o cliente; a validação final acontece no servidor
            avisoIndisponivel.classList.remove('show');
            botaoSubmit.disabled = false;
        }
    }

    [campoData, campoBarbeiro, campoHorario].forEach(function (campo) {
        campo.addEventListener('change', checarDisponibilidade);
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        botaoSubmit.disabled = true;
        botaoSubmit.textContent = 'Enviando...';

        const payload = {
            nome: document.getElementById('nome').value,
            telefone: document.getElementById('telefone').value,
            servico: document.getElementById('servico').value,
            barbeiro: campoBarbeiro.value,
            data: campoData.value,
            horario: campoHorario.value,
            observacao: document.getElementById('observacao').value,
            csrf_token: form.dataset.csrf,
            website: document.getElementById('website') ? document.getElementById('website').value : '', // honeypot
        };

        try {
            const resp = await fetch(apiAgendar, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
            });
            
            let json;
            try {
                json = await resp.json();
            } catch (parseErr) {
                json = { sucesso: false, erro: 'Resposta inválida do servidor (HTTP ' + resp.status + ').' };
            }

            if (json.sucesso) {
                form.style.display = 'none';
                const successMsg = document.getElementById('successMessage');
                if (successMsg) {
                    successMsg.style.display = 'flex';
                    successMsg.classList.add('show');
                    successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                avisoIndisponivel.textContent = json.erro || 'Não foi possível concluir o agendamento. Tente novamente.';
                avisoIndisponivel.classList.add('show');
                botaoSubmit.disabled = false;
                botaoSubmit.textContent = 'Confirmar agendamento';
            }
        } catch (err) {
            avisoIndisponivel.textContent = 'Erro de conexão. Verifique sua internet e tente novamente.';
            avisoIndisponivel.classList.add('show');
            botaoSubmit.disabled = false;
            botaoSubmit.textContent = 'Confirmar agendamento';
        }
    });
});
