-- Adicionar coluna para rastrear cadastro completo
ALTER TABLE Alunos ADD COLUMN cadastro_completo BOOLEAN DEFAULT FALSE AFTER codigo_acesso;

-- Índice para queries mais rápidas
CREATE INDEX idx_alunos_cadastro_completo ON Alunos (cadastro_completo);
