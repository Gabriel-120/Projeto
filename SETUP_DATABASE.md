# TechFit - Guia de Setup do Banco de Dados MySQL

## Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Web (Apache, Nginx, ou PHP built-in)

## Instalação

### 1. Criar o Banco de Dados

Acesse seu servidor MySQL e execute:

```sql
CREATE DATABASE techfit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techfit;
```

### 2. Configurar as Credenciais

Abra o arquivo `model/connection.php` e ajuste as credenciais:

```php
private function conectar() {
    try {
        $host = 'localhost';      // Seu host MySQL
        $db = 'techfit';          // Nome do banco de dados
        $user = 'root';           // Seu usuário MySQL
        $password = '';           // Sua senha MySQL
        // ...
```

### 3. Inicializar as Tabelas

As tabelas serão criadas automaticamente na primeira execução do script. Para forçar a criação manual:

Crie um arquivo `setup.php` na raiz do projeto:

```php
<?php
require_once 'model/connection.php';

try {
    $connection = Connection::getInstance();
    $connection->criarTabelas();
    echo "Tabelas criadas com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
```

Execute em seu navegador: `http://localhost:8000/setup.php`

## Estrutura das Tabelas

### Tabela: usuarios
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    token_reset VARCHAR(100),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabela: pagamentos
```sql
CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    plano VARCHAR(50) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pendente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_data (data_pagamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabela: recuperacao_senha
```sql
CREATE TABLE recuperacao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expiracao TIMESTAMP NOT NULL,
    utilizado BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_token (token),
    INDEX idx_expiracao (expiracao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Arquitetura

### Model Layer
- `connection.php` - Gerenciador de conexão PDO (Singleton)
- `cadastro.php` - Entidade de usuário
- `cadastroDAO.php` - Data Access Object para usuários
- `pagamento.php` - Entidade de pagamento
- `pagamentoDAO.php` - Data Access Object para pagamentos
- `recuperacaoSenha.php` - Entidade de recuperação de senha
- `recuperacaoSenhaDAO.php` - Data Access Object para recuperação

### Controller Layer
- `controller.php` - CadastroController com métodos para lógica de negócio:
  - `criar()` - Criar novo usuário com validações
  - `autenticar()` - Autenticar usuário
  - `solicitarRecuperacao()` - Solicitar recuperação de senha
  - `verificarToken()` - Verificar token
  - `redefinirSenha()` - Redefinir senha
  - `registrarPagamento()` - Registrar pagamento

### View Layer
- `Login.php` - Formulário de login com MySQL backend
- `Cadastro.php` - Formulário de cadastro (layout lado-a-lado)
- `recuperar_senha.php` - Formulário de recuperação de senha (2 etapas)
- `pagamentos.php` - Página de planos e pagamento
- `Login_Cadastro.css` - CSS com tema branco/claro
- `pagamento.css` - CSS para página de pagamentos

## Recursos

### Validações
- Email: Formato válido com filter_var()
- CPF: Algoritmo completo de validação com dígitos verificadores
- Senha: Mínimo 6 caracteres, hash com password_hash()
- Data: Formato YYYY-MM-DD

### Segurança
- PDO com prepared statements (prevenção de SQL Injection)
- Hash de senha com PASSWORD_DEFAULT
- Transações ACID
- Token único para recuperação de senha (32 bytes)
- Expiração de token (1 hora)

### Duplicação
- Email único
- CPF único
- Nome de usuário único

### Recuperação de Senha
1. Usuário entra com email e CPF
2. Sistema gera token único
3. Token é validado (não expirado, não utilizado)
4. Usuário define nova senha
5. Senha é atualizada no banco
6. Token é marcado como utilizado

## Iniciar o Servidor

### PHP Built-in Server
```bash
php -S localhost:8000 -t "Projeto TechFit"
```

### Acessar a Aplicação
- Login: `http://localhost:8000/view/Login.php`
- Cadastro: `http://localhost:8000/view/Cadastro.php`
- Recuperação: `http://localhost:8000/view/recuperar_senha.php`
- Pagamentos: `http://localhost:8000/view/pagamentos.php`

## Troubleshooting

### Erro: "Erro ao conectar ao banco de dados"
- Verifique se o MySQL está rodando
- Verifique as credenciais em `connection.php`
- Verifique se o banco `techfit` foi criado

### Erro: "Email/CPF já cadastrado"
- Isso é esperado! Significa que a validação de duplicação está funcionando

### Erro: "Token inválido ou expirado"
- O token expira após 1 hora
- Solucite nova recuperação de senha

## Boas Práticas

1. **Senhas**: Sempre use password_hash() e password_verify()
2. **SQL Injection**: Use prepared statements (PDO)
3. **CSRF**: Implemente tokens CSRF para formulários
4. **HTTPS**: Use em produção
5. **Rate Limiting**: Implemente limitação de tentativas
6. **Logs**: Registre tentativas de acesso
7. **Backup**: Faça backup regular do banco de dados

## Versão

- **Versão**: 1.0
- **Data**: Novembro de 2025
- **Framework**: Vanilla PHP com PDO
- **Banco de Dados**: MySQL 5.7+

## Suporte

Para dúvidas ou problemas, verifique:
- Os logs do PHP em `/view/` (se configurado)
- O MySQL error log
- Os arquivos `*.php` para mensagens de erro específicas
