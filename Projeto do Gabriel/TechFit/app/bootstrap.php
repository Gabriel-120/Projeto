<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/helpers/viewHelper.php';
require_once __DIR__ . '/helpers/authHelper.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/loadModels.php';
require_once __DIR__ . '/helpers/authMiddleware.php';
require_once __DIR__ . "/helpers/validators.php";

/**
 * Carrega automaticamente dados do usuário logado (se existir)
 */
function loadLoggedUser()
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $user = Usuario::getUsuarioCompleto($_SESSION['user_id']);

    if ($user) {
        $_SESSION['user_avatar'] = $user["user_avatar"];
        $_SESSION['user_name']   = $user["user_name"];
        $_SESSION['user_tipo']   = $user["user_tipo"];

        if ($user['user_tipo'] === 'funcionario') {
            $func = Funcionario::getByUsuarioId($user['user_id']);
            if ($func) {
                $_SESSION['id_funcionario'] = $func['id_funcionario'];
            }
        }
    }
}
loadLoggedUser();
