<?php
require_once __DIR__ . '/config/config.php';
$token = csrfToken();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - Barbearia Prime</title>

    <!-- Favicon (Ícone da URL e Aba) -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="alternate icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">

    <!-- Imagem de pré-visualização da URL -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Agendamento Online — Barbearia Prime">
    <meta property="og:description" content="Agende seu horário online na Barbearia Prime. Escolha seu serviço, barbeiro, data e horário.">
    <meta property="og:image" content="assets/img/og-image.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="assets/img/og-image.jpg">

    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <script src="js/theme.js?v=<?= time() ?>"></script>
</head>
<body class="client-page">

<header class="header">
    <div class="logo">BARBEARIA <span>PRIME</span></div>
    <div class="header-actions">
        <button class="theme-toggle" type="button" aria-label="Alternar tema"></button>
        <a href="index.html" class="back-link">Voltar</a>
    </div>
</header>

<main class="client-container">

<section class="client-header">
    <p class="subtitle">ÁREA DO CLIENTE</p>
    <h1>Agende seu horário</h1>
    <p>Preencha os dados abaixo para reservar seu atendimento.</p>
</section>

<section class="appointment-box">

<form id="appointmentForm" data-csrf="<?= htmlspecialchars($token) ?>" data-base-url="<?= htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\') . '/') ?>">

    <!-- Campo honeypot: invisível para humanos, usado para bloquear robôs -->
    <div style="position:absolute;left:-9999px" aria-hidden="true">
        <label for="website">Não preencha este campo</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="form-group">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" placeholder="Digite seu nome" required minlength="3">
    </div>

    <div class="form-group">
        <label for="telefone">Telefone (com DDD)</label>
        <input type="tel" id="telefone" placeholder="(75) 99999-9999" required>
    </div>

    <div class="form-group">
        <label for="servico">Serviço</label>
        <select id="servico" required>
            <option value="">Selecione um serviço</option>
            <option value="Corte Masculino - R$ 35,00">Corte Masculino - R$ 35,00</option>
            <option value="Barba - R$ 25,00">Barba - R$ 25,00</option>
            <option value="Corte + Barba - R$ 55,00">Corte + Barba - R$ 55,00</option>
            <option value="Platinado - R$ 100,00">Platinado - R$ 100,00</option>
        </select>
    </div>

    <div class="form-group">
        <label for="barbeiro">Barbeiro</label>
        <select id="barbeiro" required>
            <option value="">Selecione o profissional</option>
            <option value="Carlos">Carlos</option>
            <option value="Marcos">Marcos</option>
            <option value="João">João</option>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="data">Data</label>
            <input type="date" id="data" required>
        </div>
        <div class="form-group">
            <label for="horario">Horário</label>
            <select id="horario" required>
                <option value="">Escolha o horário</option>
                <option>08:00</option><option>09:00</option><option>10:00</option>
                <option>11:00</option><option>13:00</option><option>14:00</option>
                <option>15:00</option><option>16:00</option><option>17:00</option>
                <option>18:00</option>
            </select>
        </div>
    </div>

    <div id="avisoIndisponivel" class="field-error"></div>

    <div class="form-group">
        <label for="observacao">Observação</label>
        <textarea id="observacao" placeholder="Alguma observação? (opcional)"></textarea>
    </div>

    <button type="submit" class="btn-primary full">Confirmar agendamento</button>
</form>


<div id="successMessage" class="success-message">
    <div class="success-icon">
        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <h2>Cadastro realizado com sucesso!</h2>
    <p class="success-highlight">Seu horário foi agendado e confirmado no sistema.</p>
    <div class="success-note">
        <p><strong>Aviso importante:</strong> Caso seja necessário, nosso barbeiro entrará em contato com você.</p>
    </div>
    <p class="success-tips">Lembre-se de comparecer com alguns minutos de antecedência.</p>
    <div class="success-actions">
        <a href="cliente.php" class="btn-primary">Fazer outro agendamento</a>
        <a href="index.html" class="btn-secondary">Voltar ao início</a>
    </div>
</div>

</section>


<section class="client-info">
    <h2>Como funciona?</h2>
    <div class="steps">
        <div>
            <span>1</span>
            <h3>Escolha</h3>
            <p>Selecione seu serviço.</p>
        </div>
        <div>
            <span>2</span>
            <h3>Agende</h3>
            <p>Escolha data e horário.</p>
        </div>
        <div>
            <span>3</span>
            <h3>Compareça</h3>
            <p>Venha até nossa barbearia no horário marcado.</p>
        </div>
    </div>
</section>

</main>

<a class="whatsapp-float" href="https://wa.me/<?= WHATSAPP_NUMERO ?>?text=<?= rawurlencode(WHATSAPP_MENSAGEM_PADRAO) ?>" target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16.04 3C9.37 3 3.98 8.39 3.98 15.06c0 2.2.58 4.26 1.6 6.04L3 29l8.1-2.53a12.05 12.05 0 0 0 4.94 1.05h.01c6.67 0 12.06-5.39 12.06-12.06C28.1 8.39 22.71 3 16.04 3zm0 21.9h-.01a9.9 9.9 0 0 1-5.05-1.38l-.36-.21-4.8 1.5 1.53-4.68-.24-.38a9.86 9.86 0 0 1-1.53-5.28c0-5.47 4.45-9.92 9.93-9.92 2.65 0 5.14 1.03 7.01 2.9a9.85 9.85 0 0 1 2.9 7.02c0 5.48-4.45 9.93-9.38 9.43zm5.44-7.44c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47a8.9 8.9 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.6-.91-2.2-.24-.57-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
</a>

<button class="chat-toggle" id="chatToggle" aria-label="Abrir assistente de dúvidas">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
</button>

<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <div>
            <strong>Assistente Barbearia Prime</strong>
            <p>Tire suas dúvidas sobre agendamento, pacotes e pagamento</p>
        </div>
        <button id="chatClose" aria-label="Fechar">×</button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="chat-msg bot">Olá! Posso te ajudar com dúvidas sobre pacotes mensais, formas de pagamento e agendamento. O que você quer saber?</div>
    </div>
    <div class="chat-suggestions" id="chatSuggestions">
        <button type="button">Pacotes mensais</button>
        <button type="button">Formas de pagamento</button>
        <button type="button">Como agendar</button>
    </div>
    <div class="chat-input-row">
        <input type="text" id="chatInput" placeholder="Digite sua dúvida...">
        <button id="chatSend">Enviar</button>
    </div>
</div>

<script src="js/script.js?v=<?= time() ?>"></script>
<script src="js/agendamento.js?v=<?= time() ?>"></script>
<script src="js/chatbot.js?v=<?= time() ?>"></script>

</body>
</html>
