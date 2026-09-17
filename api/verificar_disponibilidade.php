<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$barbeiro = trim($_GET['barbeiro'] ?? '');
$data = trim($_GET['data'] ?? '');
$horario = trim($_GET['horario'] ?? '');

if ($barbeiro === '' || $data === '' || $horario === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros inválidos']);
    exit;
}

echo json_encode(['disponivel' => horarioDisponivel($barbeiro, $data, $horario)]);
