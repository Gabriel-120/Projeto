# 🔧 Troubleshooting - Guia de Resolução de Problemas

## 🚨 Erros Comuns e Soluções

### 1. "Fatal error: Uncaught PDOException: SQLSTATE[HY000]: General error: 2006"

**Causas:**
- MySQL não está rodando
- Conexão perdida com banco

**Solução:**
1. Verifique se MySQL está ativo
2. Restart MySQL
3. Teste: `mysql -u root -p -e "SELECT 1;"`

---

### 2. "Call to undefined class Connection"

**Causa:**
- `connection.php` não foi incluído

**Solução:**
```php
// Adicione no topo do arquivo:
require_once __DIR__ . '/../model/connection.php';
```

---

### 3. "UNIQUE constraint failed: usuarios.email"

**Causa:**
- Email já existe no banco

**Solução:**
- ✓ Use outro email
- ✓ Delete do banco: `DELETE FROM usuarios WHERE email='xxx';`
- ✓ Limpe tudo: `DROP DATABASE techfit;` + execute setup.php

---

### 4. "Email/CPF ou senha incorretos" (ao fazer login)

**Possíveis causas:**
1. Email/CPF errado
2. Senha errado
3. Usuário não existe

**Solução:**
1. Verifique a digitação
2. Recupere a senha se esqueceu
3. Crie uma nova conta

---

### 5. "Warning: Undefined array key"

**Causa:**
- Variável `$_POST` ou `$_GET` não existe

**Solução:**
```php
// Usar isset() para verificar
$email = isset($_POST['email']) ? $_POST['email'] : '';
```

---

### 6. "Token inválido ou expirado"

**Causa:**
- Token expira em 1 hora
- Token já foi utilizado

**Solução:**
- Solicite novo token de recuperação
- Use link antes de 1 hora

---

### 7. "Erro ao criar usuário: too long"

**Causa:**
- Valor maior que o campo permite

**Solução:**
- Verifique campo `nome` (255 caracteres máximo)
- Verifique campo `email` (255 caracteres máximo)

---

### 8. "Cannot modify header information"

**Causa:**
- Conteúdo foi enviado antes do `header()`

**Solução:**
```php
// header() deve estar ANTES de qualquer saída
session_start();  // Deve ser a primeira linha!
// Agora sim, aqui você pode fazer header()
```

---

### 9. "Connection refused" (localhost:8000)

**Causa:**
- PHP server não está rodando

**Solução:**
1. Abra PowerShell/Terminal
2. Navigate para a pasta
3. Execute: `php -S localhost:8000`

---

### 10. "This site can't be reached"

**Causa:**
- URL errada
- PHP server não rodando
- Porta bloqueada

**Solução:**
1. Verifique a URL digitada
2. Verifique se PHP está rodando
3. Tente outra porta: `php -S localhost:8001`

---

## 🔍 Debug - Como Investigar Erros

### Ver Erros do PHP

#### Windows:
```powershell
Get-Content -Path "c:\xampp\apache\logs\error.log" -Tail 20
```

#### Linux/Mac:
```bash
tail -20 /var/log/apache2/error.log
```

### Adicionar Debug no Código

```php
// Ver o que o $_POST contém
echo '<pre>';
var_dump($_POST);
echo '</pre>';
exit;

// Ver conteúdo de variável
error_log("Debug: " . print_r($usuario, true));

// Ver toda informação do banco
try {
    // seu código
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
    error_log($e->getTraceAsString());
}
```

### Verificar Banco de Dados

```sql
-- Ver todas as tabelas
SHOW TABLES;

-- Ver estrutura da tabela
DESCRIBE usuarios;

-- Ver todos os usuários
SELECT * FROM usuarios;

-- Ver erros recentes
SELECT * FROM recuperacao_senha WHERE expiracao < NOW();

-- Ver pagamentos
SELECT * FROM pagamentos;

-- Contar registros
SELECT COUNT(*) FROM usuarios;
```

---

## 🚦 Checklist de Verificação

### Antes de Usar

- [ ] MySQL está rodando?
- [ ] Banco `techfit` foi criado?
- [ ] Credenciais em `connection.php` estão corretas?
- [ ] PHP server está em `localhost:8000`?
- [ ] Browser consegue acessar `http://localhost:8000`?

### Ao Criar Conta

- [ ] Email tem @?
- [ ] CPF tem formato `000.000.000-00`?
- [ ] Data está em formato válido?
- [ ] Senha tem mínimo 6 caracteres?
- [ ] Confirmação de senha bate?

### Ao Fazer Login

- [ ] Email está correto?
- [ ] Senha está correta?
- [ ] Conta foi criada?
- [ ] Não foi bloqueado (5 tentativas)?

### Ao Recuperar Senha

- [ ] Email cadastrado existe?
- [ ] CPF informado bate com email?
- [ ] Token não expirou?
- [ ] Clicou no link antes de 1 hora?

---

## 📋 Testes de Validação

### Teste de Email

```php
// Valid
- teste@email.com ✓
- usuario+tag@domain.co.uk ✓

// Inválido
- teste@email ✗
- teste@.com ✗
- @email.com ✗
```

### Teste de CPF

```php
// Válido (exemplo real)
- 123.456.789-09 ✓

// Inválido
- 000.000.000-00 ✗ (todos iguais)
- 123.456.789-00 ✗ (dígito verificador errado)
- 123456789-09 ✗ (sem pontos)
```

### Teste de Senha

```php
// Forte
- Senh@123 ✓
- P@ssw0rd_Forte2024 ✓

// Fraca
- 123456 ⚠️ (só números)
- senha123 ⚠️ (sem maiúscula)
- SENHA ⚠️ (menos de 6 chars)
```

---

## 🔐 Verificar Segurança

### Verificar Hash de Senha

```php
// PHP
$senha = "123456";
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Verificar
if (password_verify($senha, $hash)) {
    echo "Correto!";
}
```

### Verificar SQL Injection

```php
// Inseguro (DON'T DO THIS)
$sql = "SELECT * FROM usuarios WHERE email = '" . $_POST['email'] . "'";

// Seguro (use isso)
$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$_POST['email']]);
```

---

## 💾 Backup e Restauração

### Backup do Banco

```bash
# Criar backup
mysqldump -u root -p techfit > techfit_backup.sql

# Com data no nome
mysqldump -u root -p techfit > techfit_backup_$(date +%Y%m%d).sql
```

### Restaurar Backup

```bash
# Restaurar
mysql -u root -p techfit < techfit_backup.sql

# Importar em banco vazio
mysql -u root -p -e "CREATE DATABASE techfit;"
mysql -u root -p techfit < techfit_backup.sql
```

---

## 🆘 Contatos de Suporte

### Stack Overflow
- Tag: php, mysql, pdo
- https://stackoverflow.com

### Comunidades
- PHP Brasil: https://forum.php-brasil.com
- Stack Exchange: https://pt.stackoverflow.com

### Documentação
- PHP: https://www.php.net
- MySQL: https://dev.mysql.com
- MDN Web Docs: https://developer.mozilla.org

---

## 📝 Log de Mensagens de Erro

### Erros Esperados (São Normais!)

```
✓ "Email já foi registrado" - Duplicação funcionando
✓ "Este CPF já existe" - Validação ativa
✓ "As senhas não conferem" - Validação de confirmação
✓ "Email/CPF ou senha incorretos" - Segurança de login
✓ "Token inválido ou expirado" - Expiração funcionando
```

### Erros Não Esperados (Investigar!)

```
✗ "Erro ao conectar ao banco"
✗ "Fatal error: Uncaught"
✗ "Call to undefined"
✗ "Cannot modify header"
✗ "Syntax error"
```

---

## ⚡ Performance - Otimizações

### Adicionar Índices (Já implementado)

```sql
-- Indexes já criados automaticamente:
INDEX idx_email (email)
INDEX idx_cpf (cpf)
INDEX idx_usuario (usuario_id)
INDEX idx_token (token)
INDEX idx_expiracao (expiracao)
```

### Verificar Performance

```sql
-- Ver queries lentas
SELECT * FROM mysql.slow_log;

-- Usar EXPLAIN para otimizar
EXPLAIN SELECT * FROM usuarios WHERE email = 'teste@email.com';
```

---

## 🎯 Resumo Rápido

| Erro | Solução |
|------|---------|
| MySQL recusa | Restart MySQL |
| Class not found | Adicione require_once |
| Duplicação | Use outro dado |
| Token expirado | Solicite novo |
| PHP server não roda | `php -S localhost:8000` |
| Erro de sintaxe | Verifique vírgulas/parênteses |
| Variável não existe | Use isset() para verificar |
| Header error | session_start() deve ser primeira linha |

---

## 🎓 Aprenda Mais

### Recursos Recomendados

1. **PHP Seguro**
   - https://www.php.net/manual/pt_BR/security.php

2. **MySQL**
   - https://dev.mysql.com/doc/

3. **OWASP**
   - https://owasp.org/www-project-top-ten/

4. **Web Security**
   - https://www.w3.org/Security/

---

**Última atualização**: Novembro 2025
**Status**: ✅ Completo
