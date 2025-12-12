-- Criar tabela para mensagens de tickets
-- Execute este script no MySQL para adicionar suporte a mensagens em tickets

CREATE TABLE IF NOT EXISTS TicketMensagens (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    ticket VARCHAR(255) NOT NULL,
    id_usuario INT NOT NULL,
    conteudo TEXT NOT NULL,
    data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket) REFERENCES Suporte (ticket) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE,
    INDEX idx_ticket (ticket),
    INDEX idx_usuario (id_usuario),
    INDEX idx_data (data_envio)
);
