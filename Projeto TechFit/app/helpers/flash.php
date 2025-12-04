<?php
function flash(string $message, string $type = "success"){

    // allow multiple messages per type
    if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
    if (!isset($_SESSION['flash'][$type]) || !is_array($_SESSION['flash'][$type])) {
        $_SESSION['flash'][$type] = [];
    }
    $_SESSION['flash'][$type][] = $message;

}

function get_flash($type){
    if (empty($_SESSION['flash'][$type])) return [];
    $messages = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return is_array($messages) ? $messages : [$messages];
}

function has_flash($type){
    return !empty($_SESSION['flash'][$type]);
}