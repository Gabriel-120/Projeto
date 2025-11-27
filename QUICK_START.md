# 🚀 Guia Rápido - TechFit

## Início Rápido (5 minutos)

### 1. Abra o Terminal/PowerShell

```powershell
cd c:\Users\2devt\Desktop\Gabriel Gomes\Projeto
php -S localhost:8000
```

### 2. Acesse o Setup

Abra no navegador: **http://localhost:8000/setup.php**

Clique em **"Iniciar Setup"** para criar o banco de dados.

### 3. Pronto! 🎉

Agora você pode:
- **Cadastro**: http://localhost:8000/Projeto TechFit/view/Cadastro.php
- **Login**: http://localhost:8000/Projeto TechFit/view/Login.php
- **Recuperar Senha**: http://localhost:8000/Projeto TechFit/view/recuperar_senha.php
- **Pagamentos**: http://localhost:8000/Projeto TechFit/view/pagamentos.php

## 📋 Pré-requisitos

- ✅ PHP 7.4+
- ✅ MySQL 5.7+ (rodando)
- ✅ Navegador moderno

## 🔧 Configurar MySQL (Primeira Vez)

### Se é a primeira vez usando MySQL:

1. **Abra MySQL Command Line** (ou phpMyAdmin)

2. **Crie o banco:**
```sql
CREATE DATABASE techfit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Verifique credenciais** em `Projeto TechFit/model/connection.php`:
```php
$host = 'localhost';      // Host do MySQL
$db = 'techfit';          // Nome do banco
$user = 'root';           // Seu usuário
$password = '';           // Sua senha (vazio se não tem)
```

4. **Execute setup.php** (veja item anterior)

## 👤 Testar a Aplicação

### Criar Conta
1. Vá para Cadastro
2. Preencha: Nome, Email, CPF (000.000.000-00), Data, Senha
3. Clique "Cadastrar"

### Fazer Login
1. Vá para Login
2. Use seu email e senha
3. Será redirecionado para Dashboard

### Comprar Plano
1. No Dashboard, clique "🛒 Comprar Plano"
2. Selecione um plano (Básico, Profissional, Premium)
3. Preencha dados do cartão
4. Clique "Finalizar Pagamento"

### Recuperar Senha
1. Na página de Login, clique "Esqueci minha senha"
2. Digite seu email e CPF
3. Você receberá um link com token
4. Defina uma nova senha

## 📊 Estrutura de Pastas

```
Projeto/
├── setup.php                    ← Executar primeiro!
├── Projeto TechFit/
│   ├── model/                   ← Banco de dados
│   │   ├── connection.php       ← Configurar aqui
│   │   ├── cadastro.php
│   │   ├── cadastroDAO.php
│   │   ├── pagamento.php
│   │   ├── pagamentoDAO.php
│   │   ├── recuperacaoSenha.php
│   │   └── recuperacaoSenhaDAO.php
│   ├── controller/              ← Lógica
│   │   └── controller.php
│   └── view/                    ← Interface
│       ├── Login.php            ← Página de Login
│       ├── Cadastro.php         ← Página de Cadastro
│       ├── recuperar_senha.php  ← Recuperação
│       ├── pagamentos.php       ← Planos
│       ├── index.php            ← Dashboard
│       ├── logout.php           ← Sair
│       └── Login_Cadastro.css   ← Estilos
└── SETUP_DATABASE.md            ← Documentação completa
```

## 🆘 Problemas Comuns

### "Erro ao conectar ao banco de dados"
✓ Verifique se MySQL está rodando
✓ Confira usuário/senha em `connection.php`
✓ Criou o banco `techfit`?

### "UNIQUE constraint failed: usuarios.email"
✓ Isso é bom! Significa que email já existe
✓ Use outro email para testar

### "Token inválido ou expirado"
✓ Token expira em 1 hora
✓ Solicite uma nova recuperação

### "Conexão recusada em localhost:8000"
✓ PHP server não está rodando
✓ Execute: `php -S localhost:8000`
✓ Verifique se porta 8000 está livre

## 🔐 Dados de Teste

### CPF Válido (para testar)
- `123.456.789-09` ✅

### Email
- `usuario@teste.com` ✅

### Senha
- Mínimo 6 caracteres
- Melhor: letras maiúsculas, minúsculas, números

## 💡 Dicas

1. **Sempre use HTTPS em produção** (não em desenvolvimento local)
2. **Backup do banco**: `mysqldump -u root -p techfit > backup.sql`
3. **Ver usuários cadastrados**: MySQL Workbench ou phpMyAdmin
4. **Limpar tudo**: `DROP DATABASE techfit;` + `setup.php`

## 📱 Testar no Celular

Caso queira testar pelo celular/outro PC:

1. Encontre seu IP local: `ipconfig` (Windows)
2. Use: `php -S 192.168.X.X:8000`
3. Acesse do celular: `http://192.168.X.X:8000`

## 🎨 Customizar Cores

Abra `Projeto TechFit/view/Login_Cadastro.css` e procure por:

```css
:root {
    --primary-color: #3498db;       /* Cor azul principal */
    --text-light: #1a1a1a;          /* Texto */
    --border-color: #e0e0e0;        /* Bordas */
}
```

## 📞 Suporte

1. Leia `SETUP_DATABASE.md` para mais detalhes
2. Verifique `README_ATUALIZACOES.md` para funcionalidades
3. Procure logs de erro em `error_log`

## ✅ Checklist Final

- ✓ MySQL rodando
- ✓ Banco `techfit` criado
- ✓ `connection.php` configurado
- ✓ `setup.php` executado
- ✓ PHP server rodando em localhost:8000
- ✓ Primeira conta criada
- ✓ Pode fazer login

## 🎯 Próximos Passos

1. Criar várias contas para testar
2. Testar recuperação de senha
3. Testar compra de planos
4. Verificar dados em MySQL
5. Fazer backup do banco
6. Preparar para produção (HTTPS, melhorias)

---

**Boa sorte! 🚀**

Qualquer dúvida, verifique os arquivos de documentação na pasta raiz.
