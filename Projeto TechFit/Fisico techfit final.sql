DROP DATABASE IF EXISTS Techfit;
CREATE DATABASE Techfit;
USE Techfit;

CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    cpf VARCHAR(16) NOT NULL UNIQUE,
    data_nascimento DATE,
    tipo ENUM('aluno', 'funcionario') NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NOT NULL DEFAULT '/images/upload/pfp/avatar.png'
);

CREATE TABLE Alunos (
    id_aluno INT AUTO_INCREMENT PRIMARY KEY,
    genero VARCHAR(20) NOT NULL,
    endereco TEXT NOT NULL,
    telefone VARCHAR(19) NOT NULL,
    codigo_acesso VARCHAR(100) UNIQUE,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    salario DECIMAL(8, 2) NOT NULL,
    carga_horaria INT NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE Modalidades (
    id_modalidade INT AUTO_INCREMENT PRIMARY KEY,
    nome_modalidade VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL
);

CREATE TABLE Filiais (
    id_filial INT AUTO_INCREMENT PRIMARY KEY,
    nome_filial VARCHAR(100) NOT NULL UNIQUE,
    endereco TEXT NOT NULL,
    telefone VARCHAR(16) NOT NULL
);

CREATE TABLE Aulas (
    id_aula INT AUTO_INCREMENT PRIMARY KEY,
    nome_aula VARCHAR(255) NOT NULL,
    dia_aula DATETIME NOT NULL,
    quantidade_pessoas INT NOT NULL,
    descricao TEXT NOT NULL,
    id_funcionario INT NOT NULL,
    id_modalidade INT NOT NULL,
    id_filial INT NOT NULL,
    FOREIGN KEY (id_funcionario) REFERENCES Funcionarios (id_funcionario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_modalidade) REFERENCES Modalidades (id_modalidade)
        ON DELETE RESTRICT,
    FOREIGN KEY (id_filial) REFERENCES Filiais (id_filial)
        ON DELETE RESTRICT
);

CREATE TABLE Aulas_Aluno (
    id_aula INT NOT NULL,
    id_aluno INT NOT NULL,
    PRIMARY KEY (id_aula, id_aluno),
    FOREIGN KEY (id_aula) REFERENCES Aulas (id_aula)
        ON DELETE CASCADE,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE
);

CREATE TABLE Treinos (
    id_treino INT AUTO_INCREMENT PRIMARY KEY,
    nome_treino VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    dia_treino DATETIME NOT NULL,
    id_aluno INT NOT NULL,        
    id_funcionario INT,  
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE,
    FOREIGN KEY (id_funcionario) REFERENCES Funcionarios (id_funcionario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE Avaliacoes (
    id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
    comentarios VARCHAR(255) NOT NULL,
    nota DECIMAL(4, 2) NOT NULL,
    id_aluno INT NOT NULL,
    id_funcionario INT,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE,
    FOREIGN KEY (id_funcionario) REFERENCES Funcionarios (id_funcionario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE Suporte (
    ticket VARCHAR(255) PRIMARY KEY,
    status VARCHAR(20) NOT NULL,
    categoria_suporte VARCHAR(50) NOT NULL,
    descricao_suporte VARCHAR(255) NOT NULL,
    id_aluno INT NOT NULL,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE
);

CREATE TABLE Avisos (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    tipo ENUM(
        'Comunicado',
        'Promocao',
        'Evento',
        'Manutencao',
        'MudancaHorario',
        'Novidade',
        'DicaSaude',
        'AvisoSeguranca'
    ) NOT NULL,
    conteudo TEXT NOT NULL,
    expira DATE NOT NULL,
    data_criacao DATE NOT NULL,
    anexo_path VARCHAR(255) DEFAULT NULL,
    id_funcionario INT ,
    FOREIGN KEY (id_funcionario) REFERENCES Funcionarios (id_funcionario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE Pagamentos (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(12) NOT NULL,
    data_pagamento DATETIME NOT NULL,
    valor DECIMAL(9, 2) NOT NULL,
    metodo_pagamento VARCHAR(100) NOT NULL,
    id_aluno INT NOT NULL,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE
);

CREATE TABLE Mensagens (
	id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    corpo TEXT NOT NULL,
    data_envio DATETIME NOT NULL,
    data_exclusao DATE,
    apagado BOOLEAN DEFAULT FALSE,
    id_destinatario INT NOT NULL, 
    id_remetente INT NOT NULL,    
    FOREIGN KEY (id_destinatario) REFERENCES Usuarios (id_usuario)
        ON DELETE CASCADE,
    FOREIGN KEY (id_remetente) REFERENCES Usuarios (id_usuario)
        ON DELETE CASCADE
);

CREATE TABLE Estoque (
    id_estoque INT AUTO_INCREMENT PRIMARY KEY,
    quantidade INT NOT NULL,
    tipo_produto VARCHAR(100) NOT NULL
);

CREATE TABLE Planos (
    id_plano INT AUTO_INCREMENT PRIMARY KEY,
    nome_plano VARCHAR(100) NOT NULL UNIQUE,
    descricao_plano VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    duracao INT NOT NULL
);

CREATE TABLE Planos_Aluno (
    id_aluno INT NOT NULL,
    id_plano INT NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    status ENUM('ativo','suspenso','cancelado') NOT NULL DEFAULT 'ativo',
    PRIMARY KEY (id_aluno, id_plano),
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE,
    FOREIGN KEY (id_plano) REFERENCES Planos (id_plano)
        ON DELETE CASCADE
);

CREATE TABLE Checkin (
    id_checkin INT AUTO_INCREMENT PRIMARY KEY,
    data_checkin DATETIME NOT NULL,
    id_filial INT NOT NULL,
    id_aluno INT NOT NULL,
    FOREIGN KEY (id_filial) REFERENCES Filiais (id_filial)
        ON DELETE RESTRICT,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE
);

CREATE TABLE Agendamento (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    data_agendamento DATETIME NOT NULL,
    status ENUM('agendado','cancelado','espera','presente','ausente'),
    id_aula INT NOT NULL,
    id_aluno INT NOT NULL,
    FOREIGN KEY (id_aula) REFERENCES Aulas (id_aula)
        ON DELETE CASCADE,
    FOREIGN KEY (id_aluno) REFERENCES Alunos (id_aluno)
        ON DELETE CASCADE
);

-- Usuarios
CREATE UNIQUE INDEX idx_usuarios_email ON Usuarios (email);
CREATE UNIQUE INDEX idx_usuarios_cpf   ON Usuarios (cpf);

-- Alunos
CREATE INDEX idx_alunos_usuario       ON Alunos (id_usuario);
CREATE INDEX idx_alunos_telefone      ON Alunos (telefone);
CREATE INDEX idx_alunos_codigo_acesso ON Alunos (codigo_acesso);

-- Funcionarios
CREATE INDEX idx_funcionarios_usuario ON Funcionarios (id_usuario);
CREATE INDEX idx_funcionarios_cargo   ON Funcionarios (cargo);

-- Modalidades
CREATE UNIQUE INDEX idx_modalidades_nome ON Modalidades (nome_modalidade);

-- Filiais
CREATE UNIQUE INDEX idx_filiais_nome    ON Filiais (nome_filial);
CREATE INDEX idx_filiais_telefone       ON Filiais (telefone);

-- Aulas
CREATE INDEX idx_aulas_funcionario ON Aulas (id_funcionario);
CREATE INDEX idx_aulas_modalidade  ON Aulas (id_modalidade);
CREATE INDEX idx_aulas_filial      ON Aulas (id_filial);
CREATE INDEX idx_aulas_data        ON Aulas (dia_aula);

-- Aulas_Aluno
CREATE INDEX idx_aulas_aluno_aluno ON Aulas_Aluno (id_aluno);

-- Treinos
CREATE INDEX idx_treinos_aluno       ON Treinos (id_aluno);
CREATE INDEX idx_treinos_funcionario ON Treinos (id_funcionario);
CREATE INDEX idx_treinos_data        ON Treinos (dia_treino);

-- Avaliacoes
CREATE INDEX idx_avaliacoes_aluno        ON Avaliacoes (id_aluno);
CREATE INDEX idx_avaliacoes_funcionario  ON Avaliacoes (id_funcionario);

-- Suporte
CREATE INDEX idx_suporte_aluno   ON Suporte (id_aluno);
CREATE INDEX idx_suporte_status  ON Suporte (status);
CREATE UNIQUE INDEX idx_suporte_ticket ON Suporte (ticket);

-- Avisos
CREATE INDEX idx_avisos_funcionario ON Avisos (id_funcionario);
CREATE INDEX idx_avisos_tipo        ON Avisos (tipo);
CREATE INDEX idx_avisos_expira      ON Avisos (expira);

-- Pagamentos
CREATE INDEX idx_pagamentos_aluno ON Pagamentos (id_aluno);
CREATE INDEX idx_pagamentos_status ON Pagamentos (status);
CREATE INDEX idx_pagamentos_data   ON Pagamentos (data_pagamento);

-- Mensagens
CREATE INDEX idx_mensagens_destinatario ON Mensagens (id_destinatario);
CREATE INDEX idx_mensagens_remetente    ON Mensagens (id_remetente);
CREATE INDEX idx_mensagens_envio        ON Mensagens (data_envio);

-- Estoque
CREATE INDEX idx_estoque_tipo ON Estoque (tipo_produto);

-- Planos
CREATE UNIQUE INDEX idx_planos_nome ON Planos (nome_plano);

-- Planos_Aluno
CREATE INDEX idx_planos_aluno_aluno ON Planos_Aluno (id_aluno);
CREATE INDEX idx_planos_aluno_plano ON Planos_Aluno (id_plano);
CREATE INDEX idx_planos_aluno_status ON Planos_Aluno (status);

-- Checkin
CREATE INDEX idx_checkin_filial ON Checkin (id_filial);
CREATE INDEX idx_checkin_aluno  ON Checkin (id_aluno);
CREATE INDEX idx_checkin_data   ON Checkin (data_checkin);

-- Agendamento
CREATE INDEX idx_agendamento_aluno  ON Agendamento (id_aluno);
CREATE INDEX idx_agendamento_aula   ON Agendamento (id_aula);
CREATE INDEX idx_agendamento_status ON Agendamento (status);
CREATE INDEX idx_agendamento_data   ON Agendamento (data_agendamento);
