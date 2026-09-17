document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('chatToggle');
    const chatWindow = document.getElementById('chatWindow');
    const closeBtn = document.getElementById('chatClose');
    const body = document.getElementById('chatBody');
    const input = document.getElementById('chatInput');
    const sendBtn = document.getElementById('chatSend');
    const suggestions = document.getElementById('chatSuggestions');

    if (!toggleBtn || !chatWindow) return;

    // Base de respostas por palavras-chave. A primeira regra que combinar é usada.
    const regras = [
        {
            chaves: ['pacote', 'pacotes', 'mensal', 'mensalidade', 'assinatura', 'plano', 'planos'],
            resposta: 'Temos pacotes mensais para quem corta com frequência, com condições especiais em relação ao avulso. Os valores e planos disponíveis variam conforme a demanda de cada mês — para saber os detalhes atualizados, fale com a gente pelo WhatsApp.',
        },
        {
            chaves: ['pagamento', 'pagar', 'cartão', 'cartao', 'pix', 'dinheiro', 'débito', 'debito', 'crédito', 'credito'],
            resposta: 'Aceitamos Pix, dinheiro e cartão de débito ou crédito. O pagamento é feito no local, no momento do atendimento.',
        },
        {
            chaves: ['agendar', 'agendamento', 'marcar', 'horário', 'horario', 'reservar', 'sem hora', 'chegar sem', 'encaixe'],
            resposta: 'Não realizamos atendimento sem agendamento prévio. Para garantir seu horário, use a área do cliente e escolha serviço, profissional, data e horário disponíveis.',
        },
        {
            chaves: ['cancelar', 'cancelamento', 'desmarcar'],
            resposta: 'Para cancelar ou remarcar um horário já agendado, entre em contato pelo WhatsApp informando seu nome e o horário marcado.',
        },
        {
            chaves: ['endereço', 'endereco', 'local', 'onde fica', 'localização', 'localizacao'],
            resposta: 'Estamos localizados na Conceição do Coité, 100 — Centro. Funcionamos de segunda a sábado, das 08:00 às 19:00.',
        },
        {
            chaves: ['preço', 'preco', 'valor', 'quanto custa', 'tabela'],
            resposta: 'Corte Masculino: R$ 35,00 | Barba: R$ 25,00 | Corte + Barba: R$ 55,00 | Platinado: R$ 100,00.',
        },
        {
            chaves: ['whatsapp', 'contato', 'telefone', 'falar com atendente', 'humano'],
            resposta: 'Você pode falar direto com a gente pelo WhatsApp — é só clicar no botão verde no canto da tela.',
        },
    ];

    const respostaPadrao = 'Não tenho certeza sobre isso. Posso ajudar com dúvidas sobre pacotes mensais, formas de pagamento e agendamento. Para outros assuntos, fale com a gente pelo WhatsApp.';

    function adicionarMensagem(texto, autor) {
        const div = document.createElement('div');
        div.className = 'chat-msg ' + autor;
        div.textContent = texto;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    function responder(pergunta) {
        const textoLower = pergunta.toLowerCase();
        const regra = regras.find(function (r) {
            return r.chaves.some(function (chave) { return textoLower.includes(chave); });
        });
        adicionarMensagem(regra ? regra.resposta : respostaPadrao, 'bot');
    }

    function enviar() {
        const texto = input.value.trim();
        if (!texto) return;
        adicionarMensagem(texto, 'user');
        input.value = '';
        setTimeout(function () { responder(texto); }, 350);
    }

    toggleBtn.addEventListener('click', function () {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) input.focus();
    });
    closeBtn.addEventListener('click', function () { chatWindow.classList.remove('open'); });
    sendBtn.addEventListener('click', enviar);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') enviar(); });

    if (suggestions) {
        suggestions.querySelectorAll('button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                adicionarMensagem(btn.textContent, 'user');
                setTimeout(function () { responder(btn.textContent); }, 300);
            });
        });
    }
});
