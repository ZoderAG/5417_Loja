<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require '../db.php';

// Inclui o arquivo com funções de autenticação/autorização
require '../auth.php';

// Verifica se o utilizador é um administrador
if(!isAdmin()){
    // Retorna uma resposta JSON informando que o acesso foi negado
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit(); // Encerra a execução do script
}

// Verifica se o parâmetro 'id' foi fornecido na URL (via GET)
if(!isset($_GET['id'])) {
    // Retorna uma resposta JSON informando que o ID do produto não foi fornecido
    echo json_encode(array("status" => "error", "message" => "ID do produto não fornecido"));
    exit(); // Encerra a execução do script
}

// Armazena o ID fornecido em uma variável
$id = $_GET['id'];

// Prepara a query SQL para deletar o produto com o ID especificado
$sql = $con->prepare("DELETE FROM produto WHERE id = ?");

// Associa o parâmetro à query preparada (tipo inteiro)
$sql->bind_param("i", $id);

// Executa a query
$sql->execute();

// Verifica se alguma linha foi afetada (ou seja, se o produto foi deletado com sucesso)
if($sql->affected_rows > 0){
    // Retorna uma resposta JSON de sucesso
    echo json_encode(array("status" => "success", "message" => "Produto eliminado com sucesso"));
}else{
    // Retorna uma resposta JSON de erro
    echo json_encode(array("status" => "error", "message" => "Erro ao eliminar produto"));
}

// Fecha a query preparada
$sql->close();

// Fecha a conexão com o banco de dados
$con->close();
?>
