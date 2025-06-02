<?php

// Inclui o arquivo de autenticação (funções para verificar login, etc)
require '../api/auth.php';

// Inicia a sessão para acessar variáveis de sessão
session_start();

// Verifica se o utilizador está logado
if(!isset($_SESSION["user"])){
    // Se não estiver logado, redireciona para a página de login
    header("Location: views/login.php");
    exit();
}

// Inclui o arquivo que conecta ao banco de dados
require '../api/db.php';

// Verifica se o método da requisição é POST e se os dados produtoId e quantidade foram enviados
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["produtoId"]) && isset($_POST["quantidade"])){

    // Pega o ID do utilizador logado da sessão
    $userId = $_SESSION["user"]["id"];

    // Escapa os valores para evitar injeção de SQL (apesar do uso de prepared statements, é um extra)
    $produtoId = $con->real_escape_string($_POST["produtoId"]);
    $quantidade = $con->real_escape_string($_POST["quantidade"]);

    // Se a quantidade for menor ou igual a zero, remove o produto do carrinho
    if($quantidade <= 0){
        $sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ? AND produtoId = ?");
        $sql->bind_param("ii", $userId, $produtoId);
    } else {
        // Caso contrário, atualiza a quantidade do produto no carrinho
        $sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE userId = ? AND produtoId = ?");
        $sql->bind_param("iii", $quantidade, $userId, $produtoId);
    }

    // Executa a query e verifica se foi bem sucedida
    if($sql->execute()){
        // Redireciona para a página do carrinho após a alteração
        header("Location: ../views/cart.php");
        exit();
    } else {
        // Exibe mensagem de erro caso a execução da query falhe
        echo "Erro ao atualizar carrinho.";
    }
}

?>
