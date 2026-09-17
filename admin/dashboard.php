<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
exigirLoginAdmin();

$pdo = getConexao();

$totais = $pdo->query(
    "SELECT status, COUNT(*) AS total FROM agendamentos GROUP BY status"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$filtro = $_GET['status'] ?? 'Todos';
$sql = "SELECT * FROM agendamentos";
$params = [];
if ($filtro !== 'Todos') {
    $sql .= " WHERE status = ?";
    $params[] = $filtro;
}
$sql .= " ORDER BY data_agendamento ASC, horario ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll();

$statusClasse = [
    'Agendado' => 'status-agendado', 'Concluído' => 'status-concluido',
    'Reagendado' => 'status-reagendado', 'Cancelado' => 'status-cancelado',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo — <?= NOME_NEGOCIO ?></title>
<link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
<link rel="alternate icon" type="image/png" href="../assets/img/favicon.png">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/theme.js"></script>
</head>
<body>

<header class="admin-header">
    <div class="logo">BARBEARIA <span>PRIME</span></div>
    <div class="admin-user">
        <button class="theme-toggle" type="button" aria-label="Alternar tema"></button>
        <span><?= htmlspecialchars($_SESSION['admin_nome']) ?></span>
        <a href="logout.php">Sair</a>
    </div>
</header>

<div class="admin-layout">
    <aside class="sidebar">
        <a class="active" href="dashboard.php">Agendamentos</a>
    </aside>

    <main class="admin-content">
        <div class="admin-title">
            <div>
                <p class="subtitle">PAINEL ADMINISTRATIVO</p>
                <h1>Agendamentos</h1>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="dashboard-card"><span>Agendados</span><strong><?= $totais['Agendado'] ?? 0 ?></strong></div>
            <div class="dashboard-card"><span>Concluídos</span><strong><?= $totais['Concluído'] ?? 0 ?></strong></div>
            <div class="dashboard-card"><span>Reagendados</span><strong><?= $totais['Reagendado'] ?? 0 ?></strong></div>
            <div class="dashboard-card"><span>Cancelados</span><strong><?= $totais['Cancelado'] ?? 0 ?></strong></div>
        </div>

        <section class="appointments-panel">
            <div class="panel-header">
                <h2>Lista de agendamentos</h2>
                <form method="get">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach (['Todos','Agendado','Concluído','Reagendado','Cancelado'] as $s): ?>
                            <option value="<?= $s ?>" <?= $filtro === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if (empty($agendamentos)): ?>
                <p class="no-appointments">Nenhum agendamento encontrado.</p>
            <?php endif; ?>

            <?php foreach ($agendamentos as $a):
                $whatsCliente = 'https://wa.me/55' . preg_replace('/\D/', '', $a['telefone_cliente']);
            ?>
                <div class="appointment-item">
                    <div class="appointment-top">
                        <div>
                            <h3><?= htmlspecialchars($a['nome_cliente']) ?></h3>
                            <div class="appointment-info">
                                <?= htmlspecialchars($a['servico']) ?> · <?= htmlspecialchars($a['barbeiro']) ?>
                                · <?= formatarDataBr($a['data_agendamento']) ?> às <?= htmlspecialchars($a['horario']) ?>
                                · <a class="whatsapp-link" href="<?= $whatsCliente ?>" target="_blank" rel="noopener">WhatsApp</a>
                            </div>
                        </div>
                        <span class="status <?= $statusClasse[$a['status']] ?? '' ?>"><?= $a['status'] ?></span>
                    </div>

                    <?php if (in_array($a['status'], ['Agendado', 'Reagendado'])): ?>
                        <div class="admin-actions">
                            <form method="post" action="atualizar_status.php" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <input type="hidden" name="acao" value="concluir">
                                <button type="submit">Concluir</button>
                            </form>
                            <button type="button" onclick="window.location='reagendar.php?id=<?= $a['id'] ?>'">Reagendar</button>
                            <form method="post" action="atualizar_status.php" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <input type="hidden" name="acao" value="cancelar">
                                <button type="submit" onclick="return confirm('Cancelar este agendamento?')">Cancelar</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</div>

</body>
</html>
