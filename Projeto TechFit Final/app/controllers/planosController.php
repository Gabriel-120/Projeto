<?php 

require_once __DIR__ . '/../models/Planos.php';

function planosController(): void
{
    $planos = Planos::getAll();

    $data = [
        'titulo' => 'Planos',
        'planos' => $planos,
        'headExtras' => '<link rel="stylesheet" href="/assets/css/Login_Cadastro.css">'
    ];

    render('planosView', $data['titulo'], $data);
}
