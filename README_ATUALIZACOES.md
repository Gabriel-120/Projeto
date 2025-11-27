# TechFit - Projeto Integrado com MySQL

## 🎯 Resumo das Mudanças

Este projeto foi completamente refatorado para usar MySQL como banco de dados principal, com arquitetura MVC, recuperação de senha, integração de pagamentos e novo tema branco/claro.

## 📦 Arquivos Criados/Atualizados

### Model Layer (Banco de Dados)

#### ✨ **connection.php** (NOVO)
- Gerenciador de conexão PDO (padrão Singleton)
- Métodos: executar(), buscarUm(), buscarTodos(), inserir()
- Transações: iniciarTransacao(), confirmar(), desfazer()
- Criação automática de tabelas: criarTabelas()

#### 📝 **cadastro.php** (ATUALIZADO)
- Classe melhorada com validações estáticas
- Métodos: validarCPF(), validarEmail(), validarData()
- Criptografia de senha com password_hash()
- Getters e setters refatorados

#### 📊 **cadastroDAO.php** (ATUALIZADO)
- Mudado de JSON para MySQL
- Métodos CRUD completos
- Verificação de duplicação: emailExists(), cpfExists(), nomeExists()
- Autenticação: autenticar()
- Gerenciamento de senha: atualizarSenha()

#### 💳 **pagamento.php** (NOVO)
- Entidade de pagamento simples
- Campos: usuario_id, plano, preco, status, data_pagamento

#### 💰 **pagamentoDAO.php** (NOVO)
- DAO para gerenciar pagamentos
- Métodos: criar(), buscarPorUsuario(), atualizarStatus()
- Histórico de pagamentos por usuário

#### 🔐 **recuperacaoSenha.php** (NOVO)
- Entidade para tokens de recuperação
- Token único com expiração (1 hora)
- Métodos: gerarToken(), estaExpirado(), eValido()

#### 🔑 **recuperacaoSenhaDAO.php** (NOVO)
- DAO para gerenciar recuperação de senha
- Métodos: criar(), buscarPorToken(), tokenValido()
- Marcar como utilizado: marcarUtilizado()
- Limpar expirados: deletarExpirados()

### Controller Layer

#### 🎮 **controller.php** (COMPLETO REFACTOR)
Classe `CadastroController` com métodos:
- `criar()` - Registrar novo usuário com validações
- `autenticar()` - Login com verificação de senha
- `solicitarRecuperacao()` - Iniciar processo de recuperação
- `verificarToken()` - Validar token
- `redefinirSenha()` - Redefinir senha
- `registrarPagamento()` - Registrar pagamento
- `obterPagamentos()` - Histórico de pagamentos
- `buscarTodos()` - Admin: listar todos usuários

### View Layer

#### 🔑 **Login.php** (ATUALIZADO)
- Backend MySQL integrado
- Mensagem de sucesso inline (não redireciona imediatamente)
- Link para "Esqueci minha senha"
- Validação de cliente + servidor

#### 📝 **Cadastro.php** (REDESENHADO)
- **Nova Layout**: Lado-a-lado (esquerda: dados, direita: senha)
- Todos os campos em uma única página
- Indicador de força de senha
- Validações em tempo real
- Backend MySQL com verificação de duplicação

#### 🔐 **recuperar_senha.php** (NOVO)
- **Etapa 1**: Email + CPF verificação
- **Etapa 2**: Nova senha com indicador de força
- Link de token: `recuperar_senha.php?token=xxxxx`
- Redirecionamento para login após sucesso

#### 💳 **pagamentos.php** (ATUALIZADO)
- **Novo**: Integração com MySQL para salvar pagamentos
- Planos disponíveis: Básico, Profissional, Premium
- Formatação automática de inputs (cartão, CPF, data)
- Validações de segurança
- Redirecionamento após sucesso

#### 📊 **index.php** (NOVO - Dashboard)
- Dashboard para usuário logado
- Exibição de informações pessoais
- Histórico de pagamentos
- Ações rápidas (alterar senha, logout)
- Design responsivo

#### 🚪 **logout.php** (MANTIDO)
- Destruição de sessão
- Redirecionamento para login

### CSS/Estilos

#### 🎨 **Login_Cadastro.css** (COMPLETO REDESIGN)
- **Tema**: Branco/Claro com gradiente suave
- **Sem fundo**: Removida imagem de fundo
- Cores: Azul primário (#3498db), Cinza neutro
- Formulários lado-a-lado (grid layout)
- Indicador de força de senha
- Responsivo para mobile
- Transições suaves

#### 🎨 **pagamento.css** (ATUALIZADO)
- Adaptado ao novo tema branco
- Grid layout para planos
- Responsivo para todas as telas
- Ícones de cartão (Visa, Mastercard, Elo, Hipercard)
- Validações visuais

## 📊 Estrutura do Banco de Dados

### Tabela: usuarios
```
id (PK), nome, email (UNIQUE), cpf (UNIQUE), data_nascimento, senha, token_reset, data_criacao, data_atualizacao
```

### Tabela: pagamentos
```
id (PK), usuario_id (FK), plano, preco, data_pagamento, status
```

### Tabela: recuperacao_senha
```
id (PK), usuario_id (FK), token (UNIQUE), expiracao, utilizado, data_criacao
```

## 🔐 Segurança

- ✅ PDO com prepared statements (prevenção de SQL Injection)
- ✅ password_hash() com PASSWORD_DEFAULT
- ✅ Tokens únicos para recuperação (32 bytes)
- ✅ Expiração de token (1 hora)
- ✅ Validação de email e CPF
- ✅ Prevenção de duplicação (email, CPF, nome)
- ✅ Transações ACID no banco

## 🚀 Como Usar

### 1. Configurar Banco de Dados

```php
// Editar model/connection.php com suas credenciais
$host = 'localhost';
$db = 'techfit';
$user = 'root';
$password = '';
```

### 2. Criar Tabelas

Execute na primeira vez (automático):
```bash
php -S localhost:8000 -t "Projeto TechFit"
```

Abra qualquer página e o script criará as tabelas automaticamente.

### 3. Usar a Aplicação

- **Login**: `http://localhost:8000/view/Login.php`
- **Cadastro**: `http://localhost:8000/view/Cadastro.php`
- **Recuperar Senha**: `http://localhost:8000/view/recuperar_senha.php`
- **Pagamentos**: `http://localhost:8000/view/pagamentos.php` (requer login)
- **Dashboard**: `http://localhost:8000/view/index.php` (requer login)

## ✨ Funcionalidades

### Cadastro
- ✅ Validação de email, CPF e data
- ✅ Prevenção de duplicação
- ✅ Indicador de força de senha
- ✅ Layout lado-a-lado
- ✅ Mensagens de erro detalhadas

### Login
- ✅ Autenticação com email
- ✅ Mensagem de sucesso inline
- ✅ Link para recuperação de senha
- ✅ Limite de 5 tentativas

### Recuperação de Senha
- ✅ Verificação com email + CPF
- ✅ Token único com expiração
- ✅ Redefinição de senha segura
- ✅ Indicador de força de senha

### Pagamentos
- ✅ Três planos disponíveis
- ✅ Formatação automática de inputs
- ✅ Salvamento em MySQL
- ✅ Histórico de pagamentos

### Dashboard
- ✅ Informações do usuário
- ✅ Histórico de pagamentos
- ✅ Ações rápidas
- ✅ Design responsivo

## 🎨 Tema Visual

- **Cor Primária**: #3498db (Azul)
- **Cor Secundária**: #ecf0f1 (Cinza claro)
- **Fundo**: Gradiente branco-azul
- **Texto**: #1a1a1a (Quase preto)
- **Sem imagem de fundo**
- **Logo mantida**: 120px × auto

## 📱 Responsividade

- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile (480px - 767px)
- ✅ Pequeno (< 480px)

## 🛠️ Stack Técnico

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5 + CSS3
- **JavaScript**: Vanilla (sem frameworks)
- **Padrões**: MVC, DAO, Singleton
- **Segurança**: PDO, password_hash, UNIQUE constraints

## 📝 Próximos Passos Recomendados

1. Implementar CSRF tokens nos formulários
2. Adicionar rate limiting (por IP)
3. Implementar email de confirmação
4. Adicionar autenticação 2FA
5. Implementar dashboard admin
6. Adicionar upload de avatar
7. Implementar integração de pagamento real (Stripe/PayPal)
8. Adicionar notificações por email
9. Implementar logs de auditoria
10. Configurar HTTPS

## 📄 Documentação

Veja `SETUP_DATABASE.md` para guia detalhado de instalação.

## ✅ Checklist de Funcionalidades

- ✅ Conexão MySQL centralizada
- ✅ Model com validações
- ✅ DAO com operações CRUD
- ✅ Controller com lógica de negócio
- ✅ Cadastro com layout lado-a-lado
- ✅ Login com MySQL backend
- ✅ Recuperação de senha (2 etapas)
- ✅ Pagamentos integrados
- ✅ Dashboard de usuário
- ✅ Tema branco/claro
- ✅ CSS responsivo
- ✅ Validações de segurança
- ✅ Prevenção de SQL Injection
- ✅ Hash de senha seguro
- ✅ Tokens únicos

## 📧 Suporte

Para dúvidas ou problemas:
1. Verifique o arquivo `SETUP_DATABASE.md`
2. Confira as credenciais do MySQL em `connection.php`
3. Verifique os logs do PHP
4. Teste a conexão do banco de dados

---

**Versão**: 1.0  
**Data**: Novembro de 2025  
**Autor**: Gabriel Gomes  
**Status**: ✅ Pronto para Produção (com HTTPS + melhorias recomendadas)
