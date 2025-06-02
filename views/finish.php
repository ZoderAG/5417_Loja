<?php
// Inclui o arquivo de autenticação (provavelmente contém funções para verificar login, etc)
require '../api/auth.php';

// Inicia a sessão para acessar variáveis de sessão
session_start();

// Verifica se o utilizador está logado (existe uma sessão com dados do utilizador)
if (!isset($_SESSION["user"])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: views/login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require '../api/db.php';

// Prepara uma query SQL para apagar todos os itens do carrinho do utilizador logado
$sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ?");

// Atribui o ID do utilizador atual para o parâmetro da query
$userId = $_SESSION["user"]["id"];
$sql->bind_param("i", $userId);

// Executa a query
$sql->execute();

// Verifica se algum registro foi afetado (se algum item foi removido)
if ($sql->affected_rows <= 0) {
    // Caso nenhum registro tenha sido removido, exibe uma mensagem de erro
    // Obs: pode ocorrer também se o carrinho já estava vazio
    echo "Erro ao limpar o carrinho.";
}

// Fecha a query preparada
$sql->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Obrigado pela sua compra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link para o CSS do Bootstrap para estilização rápida -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Estilo do fundo com gradiente de azul claro para preto */
        body {
            background: linear-gradient(135deg, #e3f2fd,rgb(0, 0, 0));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Card branco centralizado com sombra e borda arredondada */
        .thankyou-card {
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            background-color: white;
            text-align: center;
            max-width: 500px;
            /* Transição suave para efeitos visuais */
            transition: transform 0.3s ease-in-out, box-shadow 0.3s;
        }

        /* Efeito hover que eleva o card com sombra mais forte */
        .thankyou-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        }

        /* Botão azul personalizado com bordas arredondadas */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        /* Botão fica mais escuro ao passar o mouse */
        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        /* Estilo do título */
        h1 {
            color: #343a40;
            font-weight: 600;
        }

        /* Estilo do texto do parágrafo */
        p {
            color: #6c757d;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <!-- Container centralizado vertical e horizontalmente usando flexbox do Bootstrap -->
    <div class="thankyou-card">
        <!-- Mensagem de agradecimento -->
        <h1 class="mb-3">Obrigado pela sua compra!</h1>
        <p class="mb-4">Recebemos o seu pedido com sucesso. Em breve entraremos em contacto.</p>
        <!-- Botão que leva o usuário de volta para a página inicial -->
        <form action="/24198_Loja/index.php">
            <button type="submit" class="btn btn-primary">Voltar à página inicial</button>
        </form>
    </div>

    <!-- Script do Bootstrap para funcionalidades JS (como modal, dropdown, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
