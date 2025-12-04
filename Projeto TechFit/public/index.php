<?php


require_once __DIR__ . '/../app/helpers/viewHelper.php';
require_once __DIR__ . '/../app/helpers/authHelper.php'; 
require_once __DIR__ . '/../app/helpers/flash.php'; 
require_once __DIR__ . '/../app/helpers/loadModels.php';
session_start();

// Carregar usuário da sessão, se existir
if (isset($_SESSION['user_id'])) {
    try {
        $user = Usuario::getUsuarioCompleto($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_avatar'] = $user['user_avatar'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_tipo'] = $user['user_tipo'];

            if ($user['user_tipo'] === 'funcionario') {
                $func = Funcionario::getByUsuarioId($user['user_id']);
                if ($func) {
                    $_SESSION['id_funcionario'] = $func['id_funcionario'];
                }
            }
        }
    } catch (Exception $e) {
        // não interrompe o fluxo se houver erro ao buscar usuário
    }
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar base do script para que a rota funcione mesmo quando o index.php
// aparece na URL (ex: /meuProjeto/public/index.php)
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']); // ex: /meuProjeto/public/index.php
$baseDir = rtrim(str_replace('index.php', '', $scriptName), '/'); // ex: /meuProjeto/public

if ($baseDir !== '' && strpos($path, $baseDir) === 0) {
    $uri = substr($path, strlen($baseDir));
} else {
    $uri = $path;
}

// garantir formato '/segment' ou '' para raiz
$uri = '/' . ltrim($uri, '/');
$uri = rtrim($uri, '/');
if ($uri === '/index.php' || $uri === '/index.php/') {
    $uri = '';
}
// raiz vazia representada por string vazia (combina com rota '')
if ($uri === '/') {
    $uri = '';
}

$routes = [
    ''          => '../app/controllers/homeController.php',
    '/profile'  => '../app/controllers/profileController.php',
    '/logout'   => 'logout.php', 
    '/aulas' => '../app/controllers/aulasController.php',
    '/aulas/agendar' => '../app/controllers/aulasController.php',
    '/comunicados' => '../app/controllers/comunicadoPublicController.php',
    '/adm/comunicados' => '../app/controllers/comunicadoAdmController.php',
    '/admin' => '../app/controllers/adminController.php',
    '/admin/painel' => '../app/controllers/adminController.php',
    '/admin/usuarios' => '../app/controllers/adminController.php',
    '/sobre' => '../app/controllers/sobreController.php',
    '/contato' => '../app/controllers/contatoController.php',
    '/cadastro' => 'cadastro.php',
    '/login' => 'login.php',
    '/recuperar_senha' => 'recuperar_senha.php',
    '/pagamento' => 'pagamento.php',
    '/planos' => '../app/controllers/planosController.php',
    
];

if (array_key_exists($uri, $routes)) {
    // incluir arquivo de rota. arquivos públicos (como páginas) irão produzir saída direta;
    // controllers devem exportar uma função com o mesmo nome do arquivo para serem chamadas.
    require_once $routes[$uri];

    $controller = basename($routes[$uri], '.php');
    $function   = $controller;

    if (function_exists($function)) {
        // executa controller se definido
        $function();
    } else {
        // arquivo incluído provavelmente já imprimiu o conteúdo (página estática)
        // simplesmente encerrar para evitar enviar códigos HTTP após saída
        exit;
    }
} else {
    http_response_code(404);
    echo "Página não encontrada.";
}