<?php

function render(string $view,$tituloPagina, array $data = []){
    extract($data);
    $titulo = $tituloPagina;
    ob_start();
    require __DIR__ . "/../view/$view.php";
    $conteudo = ob_get_clean();
    require __DIR__ . "/../view/template/base.php";
}