<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
exigirLoginAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido($_POST['csrf_token'] ?? null)) {
    header('Location: dashboard.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? '';
$novoStatus = match ($acao) {
    'concluir' => 'Concluído',
    'cancelar' => 'Cancelado',
    default => null,
};

if ($id > 0 && $novoStatus) {
    $pdo = getConexao();
    $pdo->prepare('UPDATE agendamentos SET status = ? WHERE id = ?')->execute([$novoStatus, $id]);
}

header('Location: dashboard.php');
exit;
