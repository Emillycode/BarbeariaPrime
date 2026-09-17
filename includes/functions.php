<?php
require_once __DIR__ . '/../config/database.php';

/** Status que efetivamente "ocupam" um horário. Cancelados liberam a vaga. */
const STATUS_ATIVOS = "'Agendado','Reagendado'";

/** Verifica (sem travar) se já existe agendamento ativo no mesmo horário/barbeiro. */
function horarioDisponivel(string $barbeiro, string $data, string $horario, ?int $ignorarId = null): bool
{
    $pdo = getConexao();
    $sql = "SELECT id FROM agendamentos
            WHERE barbeiro = ? AND data_agendamento = ? AND horario = ?
              AND status IN (" . STATUS_ATIVOS . ")";
    $params = [$barbeiro, $data, $horario];
    if ($ignorarId) {
        $sql .= " AND id != ?";
        $params[] = $ignorarId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return !$stmt->fetch();
}

/**
 * Cria um agendamento de forma segura contra condição de corrida:
 * usa uma trava nomeada do MySQL para serializar requisições concorrentes
 * do MESMO barbeiro/data/horário antes de checar e inserir.
 * Retorna ['sucesso' => bool, 'erro' => string|null, 'id' => int|null]
 */
function criarAgendamentoSeguro(array $dados): array
{
    $pdo = getConexao();
    $nomeTrava = 'agenda_' . md5($dados['barbeiro'] . $dados['data_agendamento'] . $dados['horario']);

    $stmt = $pdo->prepare('SELECT GET_LOCK(?, 10) AS obtida');
    $stmt->execute([$nomeTrava]);
    $obtida = (bool) $stmt->fetchColumn();

    if (!$obtida) {
        return ['sucesso' => false, 'erro' => 'Sistema ocupado, tente novamente em instantes.', 'id' => null];
    }

    try {
        if (!horarioDisponivel($dados['barbeiro'], $dados['data_agendamento'], $dados['horario'])) {
            return ['sucesso' => false, 'erro' => 'Esse horário acabou de ficar indisponível. Escolha outro horário.', 'id' => null];
        }

        $ins = $pdo->prepare(
            'INSERT INTO agendamentos (nome_cliente, telefone_cliente, servico, barbeiro, data_agendamento, horario, observacao, status)
             VALUES (?,?,?,?,?,?,?,\'Agendado\')'
        );
        $ins->execute([
            $dados['nome_cliente'], $dados['telefone_cliente'], $dados['servico'],
            $dados['barbeiro'], $dados['data_agendamento'], $dados['horario'], $dados['observacao'],
        ]);

        return ['sucesso' => true, 'erro' => null, 'id' => (int) $pdo->lastInsertId()];
    } finally {
        $pdo->prepare('SELECT RELEASE_LOCK(?)')->execute([$nomeTrava]);
    }
}

function formatarDataBr(string $data): string
{
    return date('d/m/Y', strtotime($data));
}
