<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
$expirado = isset($_GET['expirado']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada, tente novamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $erro = 'Preencha e-mail e senha.';
        } else {
            $resultado = tentarLoginAdmin($email, $senha);
            if ($resultado['sucesso']) {
                header('Location: dashboard.php');
                exit;
            }
            $erro = $resultado['erro'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acesso Administrativo — <?= NOME_NEGOCIO ?></title>
<link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
<link rel="alternate icon" type="image/png" href="../assets/img/favicon.png">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/theme.js"></script>
</head>
<body class="admin-page">
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">BARBEARIA <span>PRIME</span></div>
        <h1>Acesso Administrativo</h1>
        <p class="subtitle-text">Área restrita à equipe.</p>

        <?php if ($expirado): ?><div class="alert alert-error">Sua sessão expirou. Faça login novamente.</div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn-primary full">Entrar</button>
        </form>
        <p style="text-align:center;margin-top:16px"><a href="../index.html">Voltar ao site</a></p>
    </div>
</div>
</body>
</html>
