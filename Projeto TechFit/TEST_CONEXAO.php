<?php
/**
 * TEST_CONEXAO.php - Testar conexão com banco de dados
 * Abra no navegador: http://localhost:8000/Projeto%20TechFit/TEST_CONEXAO.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/model/connection.php';

echo "<h1>🧪 Teste de Conexão com Banco de Dados</h1>";
echo "<hr>";

try {
    // 1. Conectar
    echo "<h2>1️⃣ Conectando ao banco...</h2>";
    $conexao = Connection::getInstance();
    echo "<p style='color: green;'>✅ Conexão bem-sucedida!</p>";
    
    // 2. Verificar tabelas
    echo "<h2>2️⃣ Verificando tabelas...</h2>";
    $tabelas = $conexao->verificarTabelas();
    echo "<p>Tabelas encontradas: " . count($tabelas) . "</p>";
    echo "<ul>";
    foreach ($tabelas as $tabela) {
        echo "<li><strong>$tabela</strong></li>";
    }
    echo "</ul>";
    
    // 3. Verificar se Usuarios existe
    echo "<h2>3️⃣ Verificando se tabela 'Usuarios' existe...</h2>";
    $existe = $conexao->tabelaExiste('Usuarios');
    if ($existe) {
        echo "<p style='color: green;'>✅ Tabela 'Usuarios' ENCONTRADA!</p>";
        
        // Contar usuários
        $sql = "SELECT COUNT(*) as total FROM Usuarios";
        $resultado = $conexao->buscarUm($sql);
        echo "<p>Total de usuários: <strong>" . $resultado['total'] . "</strong></p>";
        
        // Ver estrutura
        echo "<h3>Estrutura da tabela:</h3>";
        $colunas = $conexao->obterColunas('Usuarios');
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
        foreach ($colunas as $col) {
            echo "<tr>";
            echo "<td>" . $col['Field'] . "</td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . $col['Key'] . "</td>";
            echo "<td>" . $col['Default'] . "</td>";
            echo "<td>" . $col['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Tabela 'Usuarios' NÃO encontrada!</p>";
        echo "<p>Execute o script_TechFit.sql primeiro!</p>";
    }
    
    // 4. Teste de inserção
    echo "<h2>4️⃣ Testando inserção de usuário...</h2>";
    
    require_once __DIR__ . '/model/cadastro.php';
    require_once __DIR__ . '/model/cadastroDAO.php';
    
    $dao = new CadastroDAO();
    $email_teste = 'teste' . time() . '@email.com';
    
    $cadastro = new Cadastro('João Teste', $email_teste, '123.456.789-00', '1990-01-01', 'senha123');
    
    try {
        $id = $dao->criar($cadastro);
        echo "<p style='color: green;'>✅ Usuário criado com sucesso!</p>";
        echo "<p>ID: <strong>$id</strong></p>";
        echo "<p>Email: <strong>$email_teste</strong></p>";
        
        // Buscar de volta
        echo "<h3>Buscando usuário criado...</h3>";
        $usuario = $dao->buscarPorEmail($email_teste);
        if ($usuario) {
            echo "<p style='color: green;'>✅ Usuário encontrado!</p>";
            echo "<pre>";
            print_r($usuario);
            echo "</pre>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao criar usuário: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<p><strong>✅ Teste Completo!</strong> Seu banco está funcionando corretamente.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
