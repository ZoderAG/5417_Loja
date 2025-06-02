<?php

// Inclui o arquivo que gerencia autenticação (função ou sessão do utilizador)
require 'auth.php';

// Inicia a sessão para acessar variáveis de sessão como o utilizador logado
session_start();

// Verifica se o utilizador está autenticado (se existe a variável de sessão 'user')
if(!isset($_SESSION["user"])){
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../views/login.php");
    exit();
}

// Inclui o arquivo que conecta ao banco de dados
require 'db.php';

// Verifica se a requisição é POST e se os parâmetros 'produto_id' e 'quantidade' foram enviados
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    
    // Sanitiza os valores recebidos convertendo para inteiro
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);

    // Prepara uma consulta para verificar se o produto já está no carrinho deste utilizador
    $sql = $con->prepare("SELECT quantidade FROM Carrinho WHERE produtoId = ? AND userId = ?");
    $sql->bind_param("ii", $produto_id, $_SESSION['user']['id']); // vincula parâmetros produtoId e userId
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        // Se o produto já está no carrinho, obtém a quantidade atual
        $row = $result->fetch_assoc();

        // Calcula a nova quantidade somando a quantidade recebida à existente
        $nova_quantidade = $row['quantidade'] + $quantidade;

        // Prepara a query para atualizar a quantidade no carrinho
        $update_sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE produtoId = ? AND userId = ?");
        $update_sql->bind_param("iii", $nova_quantidade, $produto_id, $_SESSION['user']['id']);
        $update_sql->execute();
    } else {
        // Se o produto não está no carrinho, insere um novo registro com a quantidade
        $insert_sql = $con->prepare("INSERT INTO Carrinho (produtoId, userId, quantidade) VALUES (?, ?, ?)");
        $insert_sql->bind_param("iii", $produto_id, $_SESSION['user']['id'], $quantidade);
        $insert_sql->execute();
    }

    // Após inserir ou atualizar, redireciona para a página principal
    header("Location: ../index.php");
}

?>
