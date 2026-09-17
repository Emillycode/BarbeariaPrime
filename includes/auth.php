<?php
require_once __DIR__ . '/../config/config.php';

function exigirLoginAdmin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
    // Expira a sessão do admin após 2 horas de inatividade
    if (!empty($_SESSION['admin_ultimo_acesso']) && (time() - $_SESSION['admin_ultimo_acesso'] > 7200)) {
        session_unset();
        session_destroy();
        header('Location: login.php?expirado=1');
        exit;
    }
    $_SESSION['admin_ultimo_acesso'] = time();
}

/**
 * Valida e-mail/senha do admin com proteção contra força bruta:
 * após MAX_TENTATIVAS_LOGIN erradas, a conta fica bloqueada por
 * BLOQUEIO_MINUTOS minutos.
 */
function tentarLoginAdmin(string $email, string $senha): array
{
    try {
        $pdo = getConexao();
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? AND ativo = 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
    } catch (PDOException $e) {
        return ['sucesso' => false, 'erro' => 'Banco de dados ainda não inicializado. Acesse /install.php para configurar o acesso inicial.'];
    }

    // Resposta genérica evita revelar se o e-mail existe ou não
    $erroGenerico = 'E-mail ou senha inválidos.';

    if (!$admin) {
        return ['sucesso' => false, 'erro' => $erroGenerico];
    }

    if (!empty($admin['bloqueado_ate']) && strtotime($admin['bloqueado_ate']) > time()) {
        $minutosRestantes = ceil((strtotime($admin['bloqueado_ate']) - time()) / 60);
        return ['sucesso' => false, 'erro' => "Conta temporariamente bloqueada por excesso de tentativas. Tente novamente em {$minutosRestantes} min."];
    }

    if (password_verify($senha, $admin['senha_hash'])) {
        // Login OK: zera as tentativas
        $pdo->prepare('UPDATE admins SET tentativas_falhas = 0, bloqueado_ate = NULL WHERE id = ?')->execute([$admin['id']]);

        session_regenerate_id(true); // previne fixação de sessão
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['admin_ultimo_acesso'] = time();

        return ['sucesso' => true];
    }

    // Senha errada: incrementa tentativas e bloqueia se necessário
    $tentativas = $admin['tentativas_falhas'] + 1;
    $bloqueadoAte = null;
    if ($tentativas >= MAX_TENTATIVAS_LOGIN) {
        $bloqueadoAte = date('Y-m-d H:i:s', strtotime('+' . BLOQUEIO_MINUTOS . ' minutes'));
    }
    $pdo->prepare('UPDATE admins SET tentativas_falhas = ?, bloqueado_ate = ? WHERE id = ?')
        ->execute([$tentativas, $bloqueadoAte, $admin['id']]);

    if ($bloqueadoAte) {
        return ['sucesso' => false, 'erro' => 'Muitas tentativas erradas. Conta bloqueada por ' . BLOQUEIO_MINUTOS . ' minutos.'];
    }

    return ['sucesso' => false, 'erro' => $erroGenerico];
}
