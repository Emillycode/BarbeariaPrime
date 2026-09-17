<?php
/**
 * install.php — execute depois de importar sql/schema.sql, para criar a
 * conta do administrador com uma senha forte definida por você.
 *
 * PROTEÇÃO: em deploys via Docker/Render, o arquivo volta a existir a cada
 * novo deploy (não adianta "apagar" fisicamente), então esta página só
 * funciona se a variável de ambiente ALLOW_INSTALL estiver definida como
 * "1". Depois de criar a conta, REMOVA essa variável de ambiente e faça
 * um novo deploy — a página passa a responder 404/bloqueada.
 */
require_once __DIR__ . '/config/database.php';

$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'], true);

if (!$isLocal && env('ALLOW_INSTALL') !== '1') {
    http_response_code(404);
    die('Não encontrado.');
}

$mensagem = '';
$erro = '';

function senhaForte(string $senha): bool
{
    return strlen($senha) >= 8
        && preg_match('/[A-Z]/', $senha)
        && preg_match('/[a-z]/', $senha)
        && preg_match('/[0-9]/', $senha);
}

function garantirTabelasCriadas(PDO $pdo): void
{
    $existe = false;
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
        $existe = ($stmt->fetch() !== false);
    } catch (Exception $e) {
        $existe = false;
    }

    if (!$existe) {
        $schemaFile = __DIR__ . '/sql/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            // Divide o script SQL em comandos individuais
            $linhas = explode("\n", $sql);
            $buffer = '';
            foreach ($linhas as $linha) {
                $linhaTrim = trim($linha);
                if ($linhaTrim === '' || strpos($linhaTrim, '--') === 0 || strpos($linhaTrim, '/*') === 0) {
                    continue;
                }
                $buffer .= $linha . "\n";
                if (substr($linhaTrim, -1) === ';') {
                    $comando = trim($buffer);
                    $buffer = '';
                    // Pula CREATE DATABASE e USE se já estiver conectado ao DB especificado
                    if (!preg_match('/^\s*(CREATE DATABASE|USE)\b/i', $comando)) {
                        try {
                            $pdo->exec($comando);
                        } catch (PDOException $e) {
                            // Ignora se já existir
                        }
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!senhaForte($senha)) {
        $erro = 'A senha deve ter pelo menos 8 caracteres, com letra maiúscula, minúscula e número.';
    } else {
        try {
            $pdo = getConexao();
            garantirTabelasCriadas($pdo);

            $hash = password_hash($senha, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
            $stmt->execute([$email]);
            $existente = $stmt->fetch();

            if ($existente) {
                $pdo->prepare('UPDATE admins SET nome=?, senha_hash=?, ativo=1, tentativas_falhas=0, bloqueado_ate=NULL WHERE id=?')
                    ->execute([$nome, $hash, $existente['id']]);
                $mensagem = 'Conta atualizada com sucesso! Acesse /admin/login.php.';
            } else {
                $pdo->prepare('INSERT INTO admins (nome, email, senha_hash) VALUES (?,?,?)')
                    ->execute([$nome, $email, $hash]);
                $mensagem = 'Conta de administrador criada com sucesso! Acesse /admin/login.php.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instalação — Barbearia Prime</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="admin-page">
<div class="login-wrapper">
    <div class="login-card">
        <h1>Instalação inicial</h1>
        <p class="subtitle-text">Crie a conta do administrador. Depois de concluir, apague install.php do servidor.</p>

        <?php if ($mensagem): ?><div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>E-mail de acesso</label>
                <input type="email" name="email" required placeholder="admin@barbeariaprime.com.br">
            </div>
            <div class="form-group">
                <label>Senha (mín. 8 caracteres, com maiúscula, minúscula e número)</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" class="btn-primary full">Criar / atualizar conta</button>
        </form>
        <p style="text-align:center;margin-top:16px"><a href="admin/login.php">Ir para o login administrativo</a></p>
    </div>
</div>
</body>
</html>
