<?php 
function contatoController(){
    $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($uri === '/contato/send' || $uri === '/contato')) {
        contatoSend();
        return;
    }

    render("contatoView", "Contato");
}

function contatoSend(){
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    $errors = [];
    if ($nome === '') {
        $errors[] = 'O nome é obrigatório.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }
    if ($mensagem === '') {
        $errors[] = 'A mensagem não pode ficar vazia.';
    }

    if (!empty($errors)) {
        $data = [
            'errors' => $errors,
            'old' => [
                'nome' => htmlspecialchars($nome),
                'email' => htmlspecialchars($email),
                'assunto' => htmlspecialchars($assunto),
                'telefone' => htmlspecialchars($telefone),
                'mensagem' => htmlspecialchars($mensagem),
            ]
        ];
        render('contatoView', 'Contato', $data);
        return;
    }
    header('Location: /contato');
    exit;
}