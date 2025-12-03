<?php
function homeController(): void
{
    $data = [
        'titulo' => 'Página Inicial',
        'headExtras' => <<<HTML
            <link rel="stylesheet" href="/assets/css/home.css">
            <link rel="stylesheet" href="/assets/css/utility.css">
            <link rel="shortcut icon" href="/assets/icons/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
            <script src="/assets/js/script.js" defer></script>
        HTML
    ];

    render('homeView', $data['titulo'], $data);
}
