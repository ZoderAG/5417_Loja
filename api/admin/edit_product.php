<?php
// Inicia a sessão para manter as informações do usuário logado
session_start();

// Inclui o arquivo de conexão com o banco de dados
require '../db.php';

// Inclui o arquivo de autenticação/autorização (presumivelmente com a função isAdmin())
require '../auth.php';

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Verifica se os dados obrigatórios foram enviados via POST
if (!isset($_POST['id']) || !isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit(); // Encerra a execução do script se faltar algum dado
}

// Verifica se o utizador atual é um administrador
if (!isAdmin()) {
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit(); // Bloqueia o acesso se não for admin
}

// Recolhe e centraliza os dados enviados via POST
$id = intval($_POST['id']); // Converte o ID para inteiro
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco']; // Espera-se que seja um valor decimal

// Verifica se foi enviado um arquivo de imagem com a requisição
if (isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0) {
    // Lê o conteúdo binário da imagem enviada
    $imagem = file_get_contents($_FILES['imagem']['tmp_name']);

    // Prepara a query para atualizar todos os campos, incluindo a imagem
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?");

    // Associa os parâmetros à query preparada
    $sql->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);

    // Envia os dados binários da imagem separadamente
    $sql->send_long_data(3, $imagem); // O índice 3 refere-se ao quarto parâmetro (imagem)
} else {
    // Se não houver imagem nova, atualiza apenas os outros campos
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=? WHERE id=?");
    $sql->bind_param("ssdi", $nome, $descricao, $preco, $id);
}

// Executa a query
$sql->execute();

// Verifica se houve alguma alteração no banco
if ($sql->affected_rows > 0) {
    echo json_encode(array("status" => "success", "message" => "Produto atualizado com sucesso"));
} else {
    // Pode significar que os dados enviados são os mesmos ou que ocorreu um erro
    echo json_encode(array("status" => "error", "message" => "Nenhuma alteração feita ou erro ao atualizar produto"));
}

// Fecha a query preparada e a conexão com o banco
$sql->close();
$con->close();
?>
