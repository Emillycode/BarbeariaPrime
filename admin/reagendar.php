<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
exigirLoginAdmin();

$pdo = getConexao();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM agendamentos WHERE id = ?');
$stmt->execute([$id]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf_token'] ?? null)) {
        $erro = 'Sessão expirada, tente novamente.';
    } else {
        $novaData = $_POST['data_agendamento'];
        $novoHorario = $_POST['horario'];

        if (!horarioDisponivel($agendamento['barbeiro'], $novaData, $novoHorario, $id)) {
            $erro = 'Esse horário já está ocupado para este barbeiro. Escolha outro.';
        } else {
            $pdo->prepare("UPDATE agendamentos SET data_agendamento=?, horario=?, status='Reagendado' WHERE id=?")
                ->execute([$novaData, $novoHorario, $id]);
            header('Location: dashboard.php');
            exit;
        }
    }
}

$horarios = ['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00','18:00'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reagendar — <?= NOME_NEGOCIO ?></title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-page">
<div class="login-wrapper">
    <div class="login-card" style="max-width:420px">
        <h1>Reagendar horário</h1>
        <p class="subtitle-text"><?= htmlspecialchars($agendamento['nome_cliente']) ?> — <?= htmlspecialchars($agendamento['servico']) ?> com <?= htmlspecialchars($agendamento['barbeiro']) ?></p>

        <?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label>Nova data</label>
                <input type="date" name="data_agendamento" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($agendamento['data_agendamento']) ?>">
            </div>
            <div class="form-group">
                <label>Novo horário</label>
                <select name="horario" required>
                    <?php foreach ($horarios as $h): ?>
                        <option value="<?= $h ?>" <?= $agendamento['horario'] === $h ? 'selected' : '' ?>><?= $h ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary full">Confirmar novo horário</button>
        </form>
        <p style="text-align:center;margin-top:16px"><a href="dashboard.php">Cancelar</a></p>
    </div>
</div>
</body>
</html>
