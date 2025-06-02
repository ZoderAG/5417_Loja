<?php
// Inclui o arquivo de conexão com o banco de dados
require_once '../db.php';

// Coleta os dados do formulário enviados via POST
$nome = $_POST['nome'];
$preco = floatval($_POST['preco']); // Converte o valor de preço para float (caso venha como string)
$descricao = $_POST['descricao'];
$imagem = null; // Inicializa a variável imagem como nula

// Verifica se um arquivo de imagem foi enviado e se não houve erro no upload
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    // Caminho temporário do arquivo
    $imagemTmp = $_FILES['imagem']['tmp_name'];

    // Lê o conteúdo binário da imagem
    $imagem = file_get_contents($imagemTmp);
}

// Prepara a query SQL para inserir os dados na tabela 'produto'
$stmt = $con->prepare("INSERT INTO produto (nome, preco, descricao, imagem) VALUES (?, ?, ?, ?)");

// Associa os parâmetros à query preparada
$stmt->bind_param("sdss", $nome, $preco, $descricao, $imagem);

// Inicializa um array para armazenar a resposta da operação
$response = [];

// Executa a query e verifica se foi bem-sucedida
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Produto inserido com sucesso!';
} else {
    // Em caso de erro, armazena a mensagem de erro retornada pelo MySQL
    $response['status'] = 'error';
    $response['message'] = 'Erro ao inserir produto: ' . $stmt->error;
}

// Fecha a query preparada e a conexão com o banco
$stmt->close();
$con->close();

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Converte o array de resposta para JSON e imprime
echo json_encode($response);
