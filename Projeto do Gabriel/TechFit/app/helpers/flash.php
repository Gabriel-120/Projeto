<?php
function flash(string $message, string $type = "success"){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    if (!isset($_SESSION['flash'][$type]) || !is_array($_SESSION['flash'][$type])) {
        $_SESSION['flash'][$type] = [];
    }

    $_SESSION['flash'][$type][] = $message;
}

function get_flash($type){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    if (empty($_SESSION['flash'][$type])) {
        return [];
    }

    $messages = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return $messages;
}

function has_flash($type){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    return !empty($_SESSION['flash'][$type]) && is_array($_SESSION['flash'][$type]) && count($_SESSION['flash'][$type]) > 0;
}