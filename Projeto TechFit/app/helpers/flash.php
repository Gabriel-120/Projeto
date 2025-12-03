<?php
function flash(string $message, string $type = "success"){

    $_SESSION["flash"][$type] = $message;

}

function get_flash($type){
    $message = $_SESSION['flash'][$type];
    unset ($_SESSION['flash'][$type]);
    return $message;
}

function has_flash($type){
    return !empty($_SESSION['flash'][$type]);
}