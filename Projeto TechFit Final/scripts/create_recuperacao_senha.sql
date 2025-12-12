-- Cria tabela de recuperação de senha se não existir
CREATE TABLE IF NOT EXISTS recuperacao_senha (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token VARCHAR(128) NOT NULL UNIQUE,
  expiracao DATETIME NOT NULL,
  utilizado TINYINT(1) NOT NULL DEFAULT 0,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES Usuarios(id_usuario)
  ON DELETE CASCADE
);

