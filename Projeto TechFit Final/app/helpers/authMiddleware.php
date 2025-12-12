<?php

/**
 * Verifica se o usuário está autenticado
 * Se não estiver, redireciona para login
 */
function requireAuth(): void
{
    if (!isset($_SESSION['user_id'])) {
        flash("Você precisa estar logado para acessar esta página", "error");
        header('Location: /login');
        exit;
    }
}

/**
 * Verifica se o usuário é um funcionário/admin
 * Se não for, redireciona para home
 */
function requireFuncionario(): void
{
    requireAuth();
    
    if ($_SESSION['user_tipo'] !== 'funcionario') {
        flash("Acesso negado. Apenas funcionários podem acessar esta área", "error");
        header('Location: /');
        exit;
    }
}

/**
 * Verifica se o usuário é um aluno
 * Se não for, redireciona para home
 */
function requireAluno(): void
{
    requireAuth();
    
    if ($_SESSION['user_tipo'] !== 'aluno') {
        flash("Acesso negado. Apenas alunos podem acessar esta área", "error");
        header('Location: /');
        exit;
    }
}

/**
 * Verifica se o usuário está desautenticado
 * Se estiver logado, redireciona para home
 */
function requireGuest(): void
{
    if (isset($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }
}
