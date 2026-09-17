<?php
require_once __DIR__ . '/database.php';

define('NOME_NEGOCIO', env('NOME_NEGOCIO', 'Barbearia Prime'));
define('WHATSAPP_NUMERO', env('WHATSAPP_NUMERO', '5571997063936')); // formato E.164 sem "+"
define('WHATSAPP_MENSAGEM_PADRAO', env('WHATSAPP_MENSAGEM_PADRAO', 'Olá, barbearia prime. Estou com dúvidas'));

// Login do admin: bloqueia a conta após várias tentativas erradas
define('MAX_TENTATIVAS_LOGIN', 5);
define('BLOQUEIO_MINUTOS', 15);

date_default_timezone_set('America/Bahia');

// ------------------------------------------------------------
// Sessão segura
// ------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    // O Render (e a maioria dos PaaS) termina o HTTPS num proxy na frente
    // da aplicação, então $_SERVER['HTTPS'] não é setado mesmo com o site
    // rodando em https:// — por isso também checamos X-Forwarded-Proto.
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,   // exige HTTPS em produção
        'httponly' => true,     // JS não consegue ler o cookie de sessão
        'samesite' => 'Lax',
    ]);
    session_name('barbearia_admin_sid');
    session_start();
}

/** Gera (ou reaproveita) o token CSRF da sessão atual. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Valida um token CSRF recebido de um formulário/AJAX. */
function csrfValido(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizar(?string $valor): string
{
    return htmlspecialchars(trim((string) $valor), ENT_QUOTES, 'UTF-8');
}
