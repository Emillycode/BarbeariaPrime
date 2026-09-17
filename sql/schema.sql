-- ============================================================
-- Barbearia Prime — Schema do Banco de Dados (MySQL / MariaDB)
-- ============================================================

CREATE DATABASE IF NOT EXISTS barbearia_prime
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE barbearia_prime;

-- ------------------------------------------------------------
-- Administradores (acesso ao painel)
-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    tentativas_falhas INT NOT NULL DEFAULT 0,
    bloqueado_ate DATETIME DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Barbeiros
-- ------------------------------------------------------------
CREATE TABLE barbeiros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO barbeiros (nome) VALUES ('Carlos'), ('Marcos'), ('João');

-- ------------------------------------------------------------
-- Serviços
-- ------------------------------------------------------------
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO servicos (nome, preco) VALUES
    ('Corte Masculino', 35.00),
    ('Barba', 25.00),
    ('Corte + Barba', 55.00),
    ('Platinado', 100.00);

-- ------------------------------------------------------------
-- Agendamentos
-- ------------------------------------------------------------
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_cliente VARCHAR(150) NOT NULL,
    telefone_cliente VARCHAR(20) NOT NULL,
    servico VARCHAR(100) NOT NULL,
    barbeiro VARCHAR(100) NOT NULL,
    data_agendamento DATE NOT NULL,
    horario VARCHAR(5) NOT NULL,
    observacao TEXT,
    status ENUM('Agendado','Concluído','Reagendado','Cancelado') NOT NULL DEFAULT 'Agendado',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_disponibilidade (barbeiro, data_agendamento, horario, status)
) ENGINE=InnoDB;

-- Observação sobre conflitos de horário:
-- A checagem de disponibilidade é feita em duas camadas em api/agendar.php:
--  1) Uma trava nomeada do MySQL (GET_LOCK) serializa requisições simultâneas
--     para o mesmo barbeiro/data/horário, evitando condição de corrida.
--  2) Uma consulta verifica se já existe agendamento ATIVO (status
--     'Agendado' ou 'Reagendado') naquele mesmo horário antes de inserir.
-- Agendamentos cancelados NÃO bloqueiam o horário para novos clientes.

-- ------------------------------------------------------------
-- Conta de administrador inicial
-- NÃO insira aqui um hash fixo (o bcrypt usa salt aleatório).
-- Depois de importar este schema, abra install.php no navegador
-- para criar a conta do administrador com e-mail e senha reais.
-- ------------------------------------------------------------
