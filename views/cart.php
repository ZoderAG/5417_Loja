<?php

require '../api/auth.php';  // Inclui o arquivo com funções de autenticação

session_start();  // Inicia a sessão para acessar variáveis de sessão

// Verifica se o usuário está logado (variável de sessão "user" existe)
if(!isset($_SESSION["user"])){
    // Se não estiver logado, redireciona para a página de login
    header("Location: views/login.php");
    exit();
}

require '../api/db.php';  // Inclui o arquivo para conexão com o banco de dados

// Prepara uma consulta SQL para buscar produtos no carrinho do usuário logado
$sql = $con->prepare("
    SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, c.quantidade 
    FROM produto p 
    JOIN Carrinho c ON p.id = c.produtoId 
    WHERE c.userId = ?
");
$sql->bind_param("i", $_SESSION["user"]["id"]); // Define o parâmetro userId na query
$sql->execute(); // Executa a query
$result = $sql->get_result(); // Obtém o resultado da query

$PAYPAL_CLIENT_ID = "";  // Variável para armazenar o client ID do PayPal (deve ser preenchida)

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carrinho de compras</title>
    <!-- Bootstrap CSS para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <!-- Navbar superior com título e botão de voltar -->
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div style="width: 75px;"></div> <!-- Espaço vazio para alinhamento -->
            <span class="navbar-brand mx-auto">Carrinho de Compras</span>
            <a href="../index.php" class="btn btn-outline-light btn-sm">
                ← Voltar
            </a>
        </div>
    </nav>

    <div class="container mt-5">

        <?php if ($result->num_rows === 0): ?>
            <!-- Se não houver produtos no carrinho, mostra mensagem -->
            <div class="alert alert-info">O seu carrinho está vazio.</div>
        <?php else: ?>

        <!-- Tabela que lista os produtos no carrinho -->
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
                    <th>Atualizar</th>
                    <th>Remover</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="width: 120px;">
                        <?php 
                            // Codifica a imagem em base64 para exibição inline
                            $image = base64_encode($row['imagem']);
                            $src = 'data:image/jpeg;base64,' . $image;
                        ?>
                        <!-- Exibe a imagem do produto -->
                        <img src="<?php echo $src ?>" alt="Imagem" class="img-fluid rounded" style="max-height: 80px;">
                    </td>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td> <!-- Nome do produto -->
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td> <!-- Descrição do produto -->
                    <td><?php echo number_format($row['preco'], 2, ',', '.'); ?> €</td> <!-- Preço unitário formatado -->

                    <td style="width: 90px;">
                        <!-- Form para atualizar a quantidade do produto no carrinho -->
                        <form action="../api/update_cart.php" method="post" class="mb-0">
                            <input type="hidden" name="produtoId" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantidade" value="<?php echo $row['quantidade']; ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                    </td>
                    <td style="width: 110px;">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Atualizar</button>
                        </form>
                    </td>

                    <td style="width: 110px;">
                        <!-- Form para remover o produto do carrinho -->
                        <form action="../api/delete_cart.php" method="post" class="mb-0">
                            <input type="hidden" name="produtoId" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100">Remover</button>
                        </form>
                    </td>

                    <td style="width: 110px;" class="text-center">
                        <!-- Calcula e exibe o subtotal (quantidade * preço) formatado -->
                        <?php echo number_format($row["quantidade"] * $row['preco'], 2, ',', '.'); ?> €
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        // Reinicia o ponteiro do resultado para calcular o total geral do pedido
        $result->data_seek(0);
        $total = 0;
        while($row = $result->fetch_assoc()) {
            $total += $row["quantidade"] * $row["preco"];
        }
        ?>

        <!-- Mostra o total do pedido formatado -->
        <div class="d-flex justify-content-end mt-4">
            <h4>Total do Pedido: <span class="badge bg-success"><?php echo number_format($total, 2, ',', '.'); ?> €</span></h4>
        </div>

        <?php endif; ?>
    </div>
    
    <!-- Container para o botão do PayPal -->
    <div class="d-flex justify-content-center my-4">
        <div id="paypal-button-container" class="w-50"></div>
    </div>

    <!-- Script do SDK do PayPal, client ID deve ser configurado -->
    <script src="<?php echo "https://www.paypal.com/sdk/js?client-id=$PAYPAL_CLIENT_ID&currency=EUR"; ?>"></script>
    <script>
        paypal.Buttons({
            // Cria a ordem de pagamento com o valor total do pedido
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>'
                        }
                    }]
                });
            },
            // Ao aprovar o pagamento, redireciona para a página de finalização
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = "finish.php";
                });
            },
            // Em caso de erro, exibe mensagem no console e alerta ao utilizador
            onError: function(err) {
                console.error('Erro no pagamento:', err);
                alert('Ocorreu um erro durante o pagamento. Tente novamente.');
            }
        }).render('#paypal-button-container'); // Renderiza o botão no container definido
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
