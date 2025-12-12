-- 1. Usuarios
INSERT INTO Usuarios (nome, cpf, email, tipo, senha_hash, avatar)
VALUES
('Ana Silva', '123.456.789-00', 'ana.silva@email.com', 'aluno', '202cb962ac59075b964b07152d234b70', DEFAULT),
('Bruno Costa', '111.222.333-44', 'bruno.costa@techfit.com', 'funcionario', '202cb962ac59075b964b07152d234b70', DEFAULT),
('Carla Dias', '222.333.444-55', 'carla.dias@email.com', 'aluno', '202cb962ac59075b964b07152d234b70', DEFAULT),
('Daniel Moreira', '555.666.777-88', 'daniel.moreira@techfit.com', 'funcionario', '202cb962ac59075b964b07152d234b70', DEFAULT);

-- 2. Modalidades
INSERT INTO Modalidades (nome_modalidade, descricao)
VALUES
('Musculação', 'Treinamento de força com pesos livres e máquinas.'),
('Yoga', 'Prática de posturas, respiração e meditação para equilíbrio.'),
('Crossfit', 'Treinamento funcional de alta intensidade.');

-- 3. Filiais
INSERT INTO Filiais (nome_filial, endereco, telefone)
VALUES
('Techfit Centro', 'Rua Principal, 123, Centro', '(11) 98765-4321'),
('Techfit Bairro Norte', 'Avenida Norte, 456, Bairro Norte', '(11) 91234-5678');

-- 4. Planos
INSERT INTO Planos (nome_plano, descricao_plano, preco, duracao)
VALUES
('Plano Básico Mensal', 'Acesso a todas as áreas de musculação.', 99.90, 30),
('Plano Premium Anual', 'Acesso total, incluindo todas as aulas.', 1199.00, 365),
('Plano Aulas', 'Acesso apenas às aulas de Yoga e Crossfit.', 79.90, 30);

-- 5. Estoque
INSERT INTO Estoque (quantidade, tipo_produto)
VALUES
(150, 'Whey Protein 1kg'),
(300, 'Barra de Cereal'),
(50, 'Camiseta Techfit M');

-- 6. Alunos
INSERT INTO Alunos (genero, endereco, telefone, codigo_acesso, id_usuario)
VALUES
('Feminino', 'Rua das Flores, 10', '(11) 98888-1111', 'ANA2025', 1),
('Feminino', 'Avenida da Paz, 20', '(11) 97777-2222', 'CARLA2025', 3);

-- 7. Funcionarios
INSERT INTO Funcionarios (salario, carga_horaria, cargo, id_usuario)
VALUES
(3500.00, 40, 'Instrutor', 2),
(6000.00, 44, 'Gerente', 4);

-- 8. Aulas
INSERT INTO Aulas (nome_aula, descricao, dia_aula, quantidade_pessoas, id_funcionario, id_modalidade, id_filial)
VALUES
('Yoga do Equilíbrio Interior', 'Foco em equilíbrio físico e mental com posturas e respiração.', '2025-11-15', 20, 1, 2, 1),
('Força e Resistência', 'Treino intenso de força e resistência com pesos e exercícios funcionais.', '2025-11-16', 15, 1, 3, 2);

-- 9. Aulas_Aluno
INSERT INTO Aulas_Aluno (id_aula, id_aluno)
VALUES
(1, 1),
(1, 2),
(2, 1);

-- 10. Treinos
INSERT INTO Treinos (nome_treino, descricao, dia_treino, id_aluno, id_funcionario)
VALUES
('Treino A - Foco em Pernas', 'Agachamento, Leg Press, Extensora.', '2025-11-12', 1, 1),
('Treino B - Foco em Superiores', 'Supino, Puxada, Rosca Direta.', '2025-11-13', 2, 1);

-- 11. Avaliacoes
INSERT INTO Avaliacoes (comentarios, nota, id_aluno, id_funcionario)
VALUES
('O instrutor é muito atencioso!', 9.5, 1, 1),
('A aula de Crossfit foi incrível, mas muito pesada.', 8.0, 2, 1);

-- 12. Suporte
INSERT INTO Suporte (ticket, status, categoria_suporte, descricao_suporte, id_aluno)
VALUES
('TKT-2025-001', 'Aberto', 'Equipamento', 'Leg press da filial Centro está fazendo barulho.', 1);

-- 13. Avisos
INSERT INTO Avisos (titulo, tipo, conteudo, expira, data_criacao, id_funcionario)
VALUES
('Manutenção Piscina', 'Manutencao', 'Piscina da Filial Centro fechada em 20/11.', '2025-11-21', '2025-11-12', 2),
('Black Friday Techfit!', 'Promocao', 'Descontos de 50% na anuidade do Plano Premium!', '2025-11-30', '2025-11-10', 2);

-- 14. Pagamentos
INSERT INTO Pagamentos (status, data_pagamento, valor, metodo_pagamento, id_aluno)
VALUES
('Aprovado', '2025-11-05 10:00:00', 1199.00, 'Cartão de Crédito', 1),
('Pendente', '2025-11-10 00:00:00', 99.90, 'Boleto', 2);

-- 15. Mensagens
INSERT INTO Mensagens (titulo, corpo, data_envio, id_destinatario, id_remetente)
VALUES
('Seu novo treino', 'Oi Ana, já lancei seu novo treino (Treino A).', '2025-11-12 11:00:00', 1, 2),
('Re: Seu novo treino', 'Obrigada, instrutor! Vou começar hoje mesmo.', '2025-11-12 11:05:00', 2, 1);

-- 16. Planos_Aluno
INSERT INTO Planos_Aluno (id_aluno, id_plano, data_inicio, data_fim, status)
VALUES
(1, 2, '2025-11-05', '2026-11-05', 'ativo'),
(2, 1, '2025-11-10', '2025-12-10', 'ativo');

-- 17. Checkin
INSERT INTO Checkin (data_checkin, id_filial, id_aluno)
VALUES
('2025-11-12 09:00:00', 1, 1),
('2025-11-12 15:00:00', 2, 2);

-- 18. Agendamento
INSERT INTO Agendamento (data_agendamento, status, id_aula, id_aluno)
VALUES
('2025-11-12', 'agendado', 1, 1),
('2025-11-12', 'agendado', 2, 2);
