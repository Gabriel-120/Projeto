create database TechFit;
use TechFit;

CREATE TABLE Treinos (
nome_treino VARCHAR(50) NOT NULL,
id_treino INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
descricao VARCHAR(255) NOT NULL,
dia_treino DATE NOT NULL
);

CREATE TABLE Avaliações (
comentarios VARCHAR(255) NOT NULL,
nota DECIMAL(4, 2) NOT NULL,
id_avaliacao INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
id_aluno INT,
id_funcionario INT
);

CREATE TABLE Suporte (
status_suporte VARCHAR(20) NOT NULL,
categoria_suporte VARCHAR(50) NOT NULL,
descricao_suporte VARCHAR(255) NOT NULL,
ticket VARCHAR(255) NOT NULL PRIMARY KEY,
id_aluno INT
);

CREATE TABLE Avisos (
titulo VARCHAR(100) NOT NULL,
conteudo TEXT NOT NULL,
id_alerta INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
tipo ENUM('Comunicado', 'Promocao', 'Evento', 'Manutencao', 'MudancaHorario', 'Novidade', 'DicaSaude', 'AvisoSeguranca') NOT NULL,
expira DATE NOT NULL,
data_criacao DATE NOT NULL,
id_funcionario INT
);

CREATE TABLE Pagamentos (
status_pagamento VARCHAR(12) NOT NULL,
data_pagamento DATETIME NOT NULL,
valor DECIMAL(9, 2) NOT NULL,
metodo_pagamento VARCHAR(100) NOT NULL,
id_pagamento INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
id_aluno INT
);

CREATE TABLE Usuarios (
id_usuario INT AUTO_INCREMENT NOT NULL PRIMARY KEY PRIMARY KEY,
nome varchar(100) not null,
email VARCHAR(100) NOT NULL,
cpf varchar(14) not null unique,
data_nascimento date not null,
tipo ENUM('usuario', 'funcionario') NOT NULL,
senha_hash VARCHAR(255) NOT NULL,
Avatar varchar(255) default'public/images/pfp/placeholder.png'
);

CREATE TABLE Planos (
id_plano INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
descricao_plano VARCHAR(255) NOT NULL,
nome_plano VARCHAR(100) NOT NULL,
preco DECIMAL(5,2) NOT NULL,
duracao INT NOT NULL,
id_aluno INT
);

CREATE TABLE checkin (
data_checkin DATETIME NOT NULL PRIMARY KEY PRIMARY KEY,
id_filial INT,
id_aluno INT
);

CREATE TABLE Aulas (
id_aula INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
dia_aula DATE NOT NULL,
quantidade_pessoas INT NOT NULL,
id_funcionario INT,
id_modalidade INT,
id_filial INT
);

CREATE TABLE Modalidades (
id_modalidade INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
nome_modalidade VARCHAR(100) NOT NULL,
descricao VARCHAR(255) NOT NULL
);

CREATE TABLE Mensagens (
id_mensagem INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
titulo VARCHAR(100) NOT NULL,
corpo TEXT NOT NULL,
data_envio DATETIME NOT NULL,
id_aluno INT,
id_funcionario INT
);

CREATE TABLE Estoque (
id_estoque INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
tipo_produto VARCHAR(100) NOT NULL,
quantidade INT
);

CREATE TABLE Filiais (
id_filial INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
endereco TEXT NOT NULL,
nome_filial VARCHAR(100) NOT NULL,
telefone VARCHAR(16) NOT NULL
);

CREATE TABLE Funcionarios (
id_funcionario INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
nome_funcionario VARCHAR(100) NOT NULL,
salario DECIMAL(8,2) NOT NULL,
carga_horaria INT NOT NULL,
cpf_funcionario VARCHAR(16) NOT NULL,
cargo VARCHAR(50) NOT NULL,
id_usuario INT,
FOREIGN KEY(id_usuario) REFERENCES Usuarios (id_usuario)
);

CREATE TABLE Alunos (
id_aluno INT NOT NULL AUTO_INCREMENT PRIMARY KEY PRIMARY KEY,
data_agendamento DATE NOT NULL,
data_nascimento DATE NOT NULL,
endereco TEXT NOT NULL,
nome_aluno VARCHAR(100) NOT NULL,
telefone VARCHAR(19) NOT NULL,
genero VARCHAR(8) NOT NULL,
codigo_acesso VARCHAR(100) UNIQUE,
id_usuario INT,
status_aluno ENUM('ativo', 'suspenso', 'cancelado') DEFAULT 'ativo',
data_inicio DATE NOT NULL,
data_fim DATE NOT NULL,
FOREIGN KEY(id_usuario) REFERENCES Usuarios (id_usuario)
);

CREATE TABLE Agendamento (
status_agendamento TINYINT NOT NULL DEFAULT 1,
data_agendamento DATE NOT NULL,
id_agendamento INT NOT NULL PRIMARY KEY AUTO_INCREMENT PRIMARY KEY,
id_aluno INT,
id_aula INT,
FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno),
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula)
);

CREATE TABLE Aulas_Aluno (
id_aluno INT,
id_aula INT,
FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno),
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula)
);

ALTER TABLE Avaliações ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE Avaliações ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Suporte ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE Avisos ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Pagamentos ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE Planos ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE checkin ADD FOREIGN KEY(id_filial) REFERENCES Filiais (id_filial);
ALTER TABLE checkin ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE Aulas ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);
ALTER TABLE Aulas ADD FOREIGN KEY(id_modalidade) REFERENCES Modalidades (id_modalidade);
ALTER TABLE Aulas ADD FOREIGN KEY(id_filial) REFERENCES Filiais (id_filial);
ALTER TABLE Mensagens ADD FOREIGN KEY(id_aluno) REFERENCES Alunos (id_aluno);
ALTER TABLE Mensagens ADD FOREIGN KEY(id_funcionario) REFERENCES Funcionarios (id_funcionario);

select * from usuarios;