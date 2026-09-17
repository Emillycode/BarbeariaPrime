<?php
/**
 * Conexão com o banco de dados.
 *
 * Em produção (Render, Railway, etc.) configure as credenciais via
 * variáveis de ambiente — NUNCA deixe usuário/senha reais escritos aqui.
 * Também aceita uma única variável DATABASE_URL no formato:
 *   mysql://usuario:senha@host:porta/nome_do_banco
 *
 * Localmente (Docker Compose) os valores padrão abaixo já funcionam
 * sem precisar configurar nada.
 */

function env(string $chave, string $padrao = ''): string
{
    $valor = getenv($chave);
    return $valor !== false ? $valor : $padrao;
}

$dbHostEnv = env('DB_HOST');
$databaseUrl = env('DATABASE_URL');

if ($dbHostEnv !== '') {
    define('DB_HOST', $dbHostEnv);
    define('DB_PORT', env('DB_PORT', '4000'));
    define('DB_NAME', env('DB_NAME', 'barbearia_prime'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
} elseif ($databaseUrl !== '') {
    $partes = parse_url($databaseUrl);
    define('DB_HOST', $partes['host'] ?? 'localhost');
    define('DB_PORT', (string) ($partes['port'] ?? '4000'));
    define('DB_USER', isset($partes['user']) ? urldecode($partes['user']) : 'root');
    define('DB_PASS', isset($partes['pass']) ? urldecode($partes['pass']) : '');
    define('DB_NAME', isset($partes['path']) ? ltrim($partes['path'], '/') : 'barbearia_prime');
} else {
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'barbearia_prime');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

define('DB_CHARSET', 'utf8mb4');

function getConexao(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Se for conexão remota (ex: TiDB Cloud, Aiven, etc.), habilita SSL/TLS
        if (DB_HOST !== '127.0.0.1' && DB_HOST !== 'localhost') {
            if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                $opcoes[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }
            $caPaths = [
                '/etc/ssl/certs/ca-certificates.crt',
                '/etc/pki/tls/certs/ca-bundle.crt',
                '/etc/ssl/cert.pem'
            ];
            foreach ($caPaths as $ca) {
                if (file_exists($ca)) {
                    if (defined('PDO::MYSQL_ATTR_SSL_CA')) {
                        $opcoes[PDO::MYSQL_ATTR_SSL_CA] = $ca;
                    }
                    break;
                }
            }
        }

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        } catch (PDOException $e) {
            http_response_code(500);
            $msgErro = $e->getMessage();
            $ehJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'api/') !== false);

            if ($ehJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'sucesso' => false,
                    'erro' => 'Erro ao conectar ao banco de dados: ' . $msgErro
                ]);
                exit;
            }

            $passStatus = DB_PASS !== '' ? 'Preenchida (' . strlen(DB_PASS) . ' caracteres)' : '<span style="color:#e74c3c;font-weight:bold">Vazia / Não definida</span>';
            die('<div style="font-family:Segoe UI,Tahoma,sans-serif;max-width:650px;margin:50px auto;padding:25px;border:1px solid #e74c3c;border-radius:10px;background:#fff8f8;box-shadow:0 4px 15px rgba(0,0,0,0.08);">'
                . '<h3 style="color:#c0392b;margin-top:0;display:flex;align-items:center;gap:8px;">⚠️ Erro de conexão com o banco de dados</h3>'
                . '<p style="color:#333;font-size:15px;background:#fff;padding:12px;border:1px solid #f5c6cb;border-radius:6px;word-break:break-all;"><b>Detalhe do MySQL:</b> ' . htmlspecialchars($msgErro) . '</p>'
                . '<hr style="border:0;border-top:1px solid #f1c1c6;margin:18px 0">'
                . '<h4 style="margin:0 0 10px 0;color:#555">Parâmetros utilizados pelo sistema:</h4>'
                . '<ul style="list-style:none;padding:0;margin:0;color:#444;font-size:14px;line-height:1.8;">'
                . '<li><b>DB_HOST:</b> ' . htmlspecialchars(DB_HOST) . '</li>'
                . '<li><b>DB_PORT:</b> ' . htmlspecialchars(DB_PORT) . '</li>'
                . '<li><b>DB_USER:</b> ' . htmlspecialchars(DB_USER) . '</li>'
                . '<li><b>DB_NAME:</b> ' . htmlspecialchars(DB_NAME) . '</li>'
                . '<li><b>DB_PASS:</b> ' . $passStatus . '</li>'
                . '</ul>'
                . '<p style="margin-top:20px;font-size:13px;color:#777">Verifique as variáveis de ambiente na aba <b>Environment</b> do Render e certifique-se de que os dados conferem com o TiDB.</p>'
                . '</div>');
        }
    }

    return $pdo;
}
