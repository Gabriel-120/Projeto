<?php
require_once __DIR__ . '/../controller/Controller.php';

// Instancia o controller responsável pelas operações
$controller = new LivrosController();

// Ação enviada via POST (criar, editar, deletar, atualizar)
$acao = $_POST['acao'] ?? '';
$editarLivros = null;

// Mensagens para feedback ao usuário
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($acao === 'criar') {
        // Tenta criar; o método retorna false se já existir título igual
        $ok = $controller->criar(
            trim($_POST['Titulo']),
            trim($_POST['Autor']),
            (int) $_POST['Ano'],
            trim($_POST['Genero']),
            (int) $_POST['Quantidade']
        );
        if ($ok === false) {
            $error = 'Já existe um livro cadastrado com este título.';
        } else {
            // redireciona para limpar o POST e mostrar a lista atualizada
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    if ($acao === 'deletar') {
        // Exclui sem validação extra
        $controller->deletar(trim($_POST['Titulo']));
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if ($acao === 'editar') {
        // Carrega os dados do livro para preencher o formulário de edição
        $editarLivros = $controller->buscarPorTitulo(trim($_POST['Titulo']));
    }

    if ($acao === 'atualizar') {
        // Tenta atualizar; método retorna false se o novo título já existir
        $ok = $controller->atualizar(
            trim($_POST['titulo_Original']),
            trim($_POST['Titulo']),
            trim($_POST['Autor']),
            (int) $_POST['Ano'],
            trim($_POST['Genero']),
            (int) $_POST['Quantidade']
        );
        if ($ok === false) {
            $error = 'Não foi possível atualizar: já existe um livro com o título informado.';
            // preservar os valores submetidos para reexibi-los no formulário de edição
            $preservePost = $_POST;
        } else {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

$lista = $controller->ler();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gerenciamento de Livros</title>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo">
            <div class="mark">B</div>
            <div>
                <div class="title">Genrenciamento de Livros</div>
                <div class="subtitle">Painel de Controle - Somativa</div>
            </div>
        </div>

            <!-- Feedback: exibe mensagens de erro ou sucesso -->
            <?php if (!empty($error)): ?>
                <div style="color:#ffb3b3;margin-bottom:12px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div style="color:#b8ffbd;margin-bottom:12px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
    </div>

<?php if ($editarLivros || isset($preservePost)): ?>

    <form method="post">
        <input type="hidden" name="acao" value="atualizar">
        <input type="hidden" name="titulo_Original" value="<?= htmlspecialchars($editarLivros ? $editarLivros->getTitulo() : ($preservePost['titulo_Original'] ?? '')) ?>">

        <input type="text" name="Titulo" placeholder="Título do livro" required value="<?= htmlspecialchars($editarLivros ? $editarLivros->getTitulo() : ($preservePost['Titulo'] ?? '')) ?>">
        <input type="text" name="Autor" placeholder="Nome do autor" required value="<?= htmlspecialchars($editarLivros ? $editarLivros->getAutor() : ($preservePost['Autor'] ?? '')) ?>">
        <input type="number" name="Ano" placeholder="Ano do lançamento" required value="<?= htmlspecialchars($editarLivros ? $editarLivros->getAno_publicacao() : ($preservePost['Ano'] ?? '')) ?>">
        <input type="text" name="Genero" placeholder="Genero do livro" required value="<?= htmlspecialchars($editarLivros ? $editarLivros->getGenero() : ($preservePost['Genero'] ?? '')) ?>">
        <input type="number" name="Quantidade" placeholder="Quantidade disponivel" required value="<?= htmlspecialchars($editarLivros ? $editarLivros->getQuantidade() : ($preservePost['Quantidade'] ?? '')) ?>">

        <button type="submit">Atualizar</button>
    </form>

<?php else: ?>
    <form method="post">
        <input type="hidden" name="acao" value="criar">
        <input type="text" name="Titulo" placeholder="Título do livro" required>
        <input type="text" name="Autor" placeholder="Nome do autor" required>
        <input type="number" name="Ano" placeholder="Ano do lançamento" required>
        <input type="text" name="Genero" placeholder="Genero do livro" required>
        <input type="number" name="Quantidade" placeholder="Quantidade disponivel" required>

        <button type="submit">Cadastrar</button>
    </form>

<?php endif; ?>

<hr>
<br><br>

<h2>Lista de Livros</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Ano Lançamento</th>
        <th>Genero</th>
        <th>Quantidade</th>
        <th>Ações</th>
    </tr>

    <?php if (!empty($lista)): ?>
        <?php foreach ($lista as $livros): ?>
            <tr>
                <td><?= htmlspecialchars($livros->getTitulo()) ?></td>
                <td><?= htmlspecialchars($livros->getAutor()) ?></td>
                <td><?= htmlspecialchars($livros->getAno_publicacao()) ?></td>
                <td><?= htmlspecialchars($livros->getGenero()) ?></td>
                <td><?= htmlspecialchars($livros->getQuantidade()) ?></td>
                <td>

                    <form method="post" style="display: inline;">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="Titulo" value="<?= htmlspecialchars($livros->getTitulo()) ?>">
                        <input type="submit" class="edit" value="Editar">
                    </form>

                    <form method="post" style="display: inline;">
                        <input type="hidden" name="acao" value="deletar">
                        <input type="hidden" name="Titulo" value="<?= htmlspecialchars($livros->getTitulo()) ?>">
                        <input type="submit" class="delete" value="Deletar" onclick="return confirm('Tem certeza que deseja deletar este livro?');">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">Nenhum livro cadastrado.</td></tr>
    <?php endif; ?>
</table>
</div>
</body>
</html>