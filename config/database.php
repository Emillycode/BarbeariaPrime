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

$databaseUrl = env('DATABASE_URL');

if ($databaseUrl !== '') {
    $partes = parse_url($databaseUrl);
    define('DB_HOST', $partes['host'] ?? 'localhost');
    define('DB_PORT', (string) ($partes['port'] ?? '3306'));
    define('DB_USER', $partes['user'] ?? 'root');
    define('DB_PASS', $partes['pass'] ?? '');
    define('DB_NAME', isset($partes['path']) ? ltrim($partes['path'], '/') : 'barbearia_prime');
} else {
    define('DB_HOST', env('DB_HOST', '127.0.0.1'));
    define('DB_PORT', env('DB_PORT', '3306'));
    define('DB_NAME', env('DB_NAME', 'barbearia_prime'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
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
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        } catch (PDOException $e) {
            http_response_code(500);
            $ehJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'api/') !== false);

            if ($ehJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'sucesso' => false,
                    'erro' => 'Erro ao conectar ao banco de dados. Verifique se o MySQL do WampServer está iniciado.'
                ]);
                exit;
            }

            die('Erro de conexão com o banco de dados. Verifique as variáveis de ambiente DB_HOST/DB_USER/DB_PASS/DB_NAME (ou DATABASE_URL).');
        }
    }

    return $pdo;
}
