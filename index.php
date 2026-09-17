<?php
require_once __DIR__ . '/config/config.php';
$versao = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(NOME_NEGOCIO) ?></title>
    <meta name="description" content="<?= htmlspecialchars(NOME_NEGOCIO) ?> — cortes, barba e cuidados masculinos. Agende seu horário online.">

    <!-- Favicon (Ícone da URL e Aba) -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="alternate icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">

    <!-- Imagem de pré-visualização da URL (WhatsApp / Redes Sociais) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars(NOME_NEGOCIO) ?> — Estilo, Qualidade e Tradição">
    <meta property="og:description" content="Cortes modernos, barba e cuidados masculinos feitos por profissionais especializados. Agende seu horário online.">
    <meta property="og:image" content="assets/img/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="675">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="assets/img/og-image.jpg">

    <link rel="stylesheet" href="css/style.css?v=<?= $versao ?>">
    <script src="js/theme.js?v=<?= $versao ?>"></script>
</head>
<body>

<header class="header">
    <div class="logo">BARBEARIA <span>PRIME</span></div>

    <nav>
        <a href="#inicio">Início</a>
        <a href="#servicos">Serviços</a>
        <a href="#sobre">Sobre</a>
        <a href="#contato">Contato</a>
        <button class="theme-toggle" type="button" aria-label="Alternar tema"></button>
        <a href="cliente.php" class="btn-login">Área do Cliente</a>
    </nav>
</header>

<main>

<section class="hero" id="inicio">
    <div class="hero-content">
        <p class="subtitle">ESTILO • QUALIDADE • TRADIÇÃO</p>
        <h1>Seu estilo começa<br><span>na cadeira.</span></h1>
        <p>Cortes modernos, barba e cuidados masculinos feitos por profissionais especializados.</p>
        <div class="hero-buttons">
            <a href="cliente.php" class="btn-primary">Agendar horário</a>
            <a href="#servicos" class="btn-secondary">Ver serviços</a>
        </div>
    </div>
</section>

<section class="services" id="servicos">
    <div class="section-title">
        <p>O QUE OFERECEMOS</p>
        <h2>Serviços</h2>
    </div>

    <div class="service-grid">
        <div class="service-card">
            <div class="service-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c99b4a" stroke-width="1.6"><circle cx="6" cy="6" r="2.4"/><circle cx="6" cy="18" r="2.4"/><path d="M8.6 7.6 19 18M8.6 16.4 19 6"/></svg></div>
            <h3>Corte Masculino</h3>
            <p>Corte tradicional ou moderno de acordo com o seu estilo.</p>
            <strong>R$ 35,00</strong>
        </div>

        <div class="service-card">
            <div class="service-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c99b4a" stroke-width="1.6"><path d="M4 4c4 1 6 5 6 9v6c0 1 1 1.5 2 1s2-1 2-2V6"/><path d="M20 5 12 13"/></svg></div>
            <h3>Barba</h3>
            <p>Modelagem e acabamento profissional para sua barba.</p>
            <strong>R$ 25,00</strong>
        </div>

        <div class="service-card">
            <div class="service-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c99b4a" stroke-width="1.6"><circle cx="6" cy="6" r="2.2"/><circle cx="6" cy="18" r="2.2"/><path d="M8.4 7.4 19 18M8.4 16.6 13 12"/><path d="M13 12l7-7"/></svg></div>
            <h3>Corte + Barba</h3>
            <p>Corte completo acompanhado de modelagem da barba.</p>
            <strong>R$ 55,00</strong>
        </div>

        <div class="service-card">
            <div class="service-icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c99b4a" stroke-width="1.6"><path d="M12 2v4M12 18v4M2 12h4M18 12h4M5 5l2.8 2.8M16.2 16.2 19 19M19 5l-2.8 2.8M7.8 16.2 5 19"/></svg></div>
            <h3>Platinado</h3>
            <p>Transformação completa com descoloração e acabamento.</p>
            <strong>R$ 100,00</strong>
        </div>
    </div>
</section>

<section class="about" id="sobre">
    <div class="about-content">
        <div>
            <p class="subtitle">SOBRE NÓS</p>
            <h2>Mais que um corte.<br>Uma experiência.</h2>
        </div>
        <div>
            <p>Na Barbearia Prime acreditamos que cuidar da aparência também é cuidar da autoestima.</p>
            <p>Nosso espaço foi criado para oferecer conforto, qualidade e atendimento personalizado.</p>
        </div>
    </div>
</section>

<section class="appointment">
    <div>
        <p class="subtitle">HORÁRIO ONLINE</p>
        <h2>Agende seu horário</h2>
        <p>Escolha o serviço, profissional, data e horário sem precisar ligar.</p>
        <a href="cliente.php" class="btn-primary">Fazer agendamento</a>
    </div>
</section>

<section class="contact" id="contato">
    <div class="section-title">
        <p>FALE CONOSCO</p>
        <h2>Contato</h2>
    </div>

    <div class="contact-grid">
        <div>
            <h3>Endereço</h3>
            <p>Conceição do Coité, 100<br>Centro</p>
        </div>
        <div>
            <h3>Telefone / WhatsApp</h3>
            <p><a class="whatsapp-link" href="https://wa.me/<?= WHATSAPP_NUMERO ?>?text=<?= rawurlencode(WHATSAPP_MENSAGEM_PADRAO) ?>" target="_blank" rel="noopener">(71) 99706-3936</a></p>
        </div>
        <div>
            <h3>Horário de funcionamento</h3>
            <p>Segunda a sábado<br>08:00 às 19:00</p>
        </div>
    </div>
</section>

</main>

<footer>
    <div class="logo">BARBEARIA <span>PRIME</span></div>
    <p>© 2026 Barbearia Prime. Todos os direitos reservados.</p>
    <a href="admin/login.php" class="admin-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        Acesso administrativo
    </a>
</footer>

<!-- Botão flutuante do WhatsApp -->
<a class="whatsapp-float" href="https://wa.me/<?= WHATSAPP_NUMERO ?>?text=<?= rawurlencode(WHATSAPP_MENSAGEM_PADRAO) ?>" target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16.04 3C9.37 3 3.98 8.39 3.98 15.06c0 2.2.58 4.26 1.6 6.04L3 29l8.1-2.53a12.05 12.05 0 0 0 4.94 1.05h.01c6.67 0 12.06-5.39 12.06-12.06C28.1 8.39 22.71 3 16.04 3zm0 21.9h-.01a9.9 9.9 0 0 1-5.05-1.38l-.36-.21-4.8 1.5 1.53-4.68-.24-.38a9.86 9.86 0 0 1-1.53-5.28c0-5.47 4.45-9.92 9.93-9.92 2.65 0 5.14 1.03 7.01 2.9a9.85 9.85 0 0 1 2.9 7.02c0 5.48-4.45 9.93-9.38 9.43zm5.44-7.44c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47a8.9 8.9 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.6-.91-2.2-.24-.57-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
</a>

<!-- Assistente de dúvidas (FAQ) -->
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

<script src="js/script.js?v=<?= $versao ?>"></script>
<script src="js/chatbot.js?v=<?= $versao ?>"></script>

</body>
</html>
