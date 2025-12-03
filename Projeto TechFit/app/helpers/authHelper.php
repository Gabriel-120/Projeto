<?php 

function logout(){
    session_destroy();
    header('Location: /');
}

function requireFuncionario()
{
    if (empty($_SESSION['user_id']) || ($_SESSION['user_tipo'] ?? null) !== 'funcionario') {
        http_response_code(403);
        echo "Acesso negado.";
        exit;
    }
}
