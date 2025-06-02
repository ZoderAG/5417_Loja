<?php
// Inclui arquivo que contém funções de autenticação (verificação de login, etc)
require '../api/auth.php';

// Inicia a sessão para acessar variáveis de sessão (utilizador logado)
session_start();

// Verifica se o utilizador está logado
if(!isset($_SESSION["user"])){
    // Se não estiver, redireciona para a página de login
    header("Location: views/login.php");
    exit();
}

// Inclui arquivo que contém a conexão com o banco de dados
require '../api/db.php';

// Verifica se a requisição é do tipo POST e se o parâmetro "produtoId" foi enviado
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["produtoId"])){

    // Pega o ID do utilizador da sessão atual
    $userId = $_SESSION["user"]["id"];

    // Escapa o valor de produtoId para evitar SQL Injection (apesar de usar prepared statement, é uma camada extra)
    $produtoId = $con->real_escape_string($_POST["produtoId"]);

    // Prepara a query para remover o produto do carrinho do utiizador
    $sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ? AND produtoId = ?");
    
    // Liga os parâmetros (dois inteiros: userId e produtoId)
    $sql->bind_param("ii", $userId, $produtoId);

    // Executa a query e verifica se deu certo
    if($sql->execute()){
        // Se deu certo, redireciona para a página do carrinho
        header("Location: ../views/cart.php");
        exit();
    } else {
        // Se deu erro, exibe mensagem simples
        echo "Erro ao remover produto do carrinho.";
    }
}
?>
