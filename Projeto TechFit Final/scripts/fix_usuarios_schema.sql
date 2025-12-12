-- Script para adicionar colunas faltantes na tabela Usuarios
-- Execute isso no MySQL para corrigir o schema

ALTER TABLE Usuarios ADD COLUMN nome VARCHAR(255) NOT NULL AFTER id_usuario;
ALTER TABLE Usuarios ADD COLUMN data_nascimento DATE AFTER cpf;

-- Verificar resultado
DESCRIBE Usuarios;
