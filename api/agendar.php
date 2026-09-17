<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (!csrfValido($dados['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada. Atualize a página e tente novamente.']);
    exit;
}

// Honeypot anti-spam: campo invisível que só um robô preencheria
if (!empty($dados['website'] ?? '')) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'Requisição inválida.']);
    exit;
}

$nome = trim($dados['nome'] ?? '');
$telefone = preg_replace('/\D/', '', $dados['telefone'] ?? '');
$servico = trim($dados['servico'] ?? '');
$barbeiro = trim($dados['barbeiro'] ?? '');
$data = trim($dados['data'] ?? '');
$horario = trim($dados['horario'] ?? '');
$observacao = trim($dados['observacao'] ?? '');

$servicosValidos = ['Corte Masculino - R$ 35,00', 'Barba - R$ 25,00', 'Corte + Barba - R$ 55,00', 'Platinado - R$ 100,00'];
$barbeirosValidos = ['Carlos', 'Marcos', 'João'];
$horariosValidos = ['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00','18:00'];

$erros = [];
if (mb_strlen($nome) < 3) $erros[] = 'Informe seu nome completo.';
if (strlen($telefone) < 10 || strlen($telefone) > 11) $erros[] = 'Informe um telefone válido com DDD.';
if (!in_array($servico, $servicosValidos, true)) $erros[] = 'Serviço inválido.';
if (!in_array($barbeiro, $barbeirosValidos, true)) $erros[] = 'Barbeiro inválido.';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || strtotime($data) < strtotime(date('Y-m-d'))) {
    $erros[] = 'Selecione uma data válida (hoje ou futura).';
}
if (!in_array($horario, $horariosValidos, true)) $erros[] = 'Horário inválido.';

if (!empty($erros)) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => implode(' ', $erros)]);
    exit;
}

$resultado = criarAgendamentoSeguro([
    'nome_cliente' => $nome,
    'telefone_cliente' => $telefone,
    'servico' => $servico,
    'barbeiro' => $barbeiro,
    'data_agendamento' => $data,
    'horario' => $horario,
    'observacao' => $observacao,
]);

echo json_encode($resultado);
