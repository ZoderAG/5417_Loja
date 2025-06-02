<?php
// Inclui funções de autenticação, como isAdmin()
require 'api/auth.php';

// Inicia a sessão para manter utilizador logado
session_start();

// Verifica se o utilizador está logado; caso contrário, redireciona para login
if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

// Inclui a conexão com o banco de dados
require 'api/db.php';

// Captura o termo da busca, se existir, e escapa caracteres especiais para evitar SQL Injection
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

// Monta a consulta SQL para buscar produtos
$sql = "SELECT id, nome, descricao, preco, imagem FROM produto";

// Se o termo de busca não estiver vazio, adiciona filtro na consulta para nome ou descrição
if ($search !== '') {
    $sql .= " WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";
}

// Executa a consulta no banco de dados
$result = $con->query($sql);

// Inicializa array para armazenar os produtos encontrados
$produtos = [];
if ($result && $result->num_rows > 0) {
    // Preenche o array com os produtos da consulta
    while($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loja - Produtos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light">

<!-- Navbar principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Loja</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if(isAdmin()){ ?>
                    <!-- Link para área do administrador, visível apenas para admins -->
                    <li class="nav-item me-2">
                        <a href="views/areaadmin.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-person-badge-fill"></i> Administrador
                        </a>
                    </li>
                <?php } ?>
                <!-- Botão de logout -->
                <li class="nav-item me-2">
                    <a href="views/logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>
                <!-- Botão para acessar carrinho -->
                <li class="nav-item">
                    <a href="views/cart.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-cart-fill"></i> Carrinho
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Container principal -->
<div class="container">

    <!-- Formulário de busca -->
    <form class="row mb-4" method="get" action="">
        <div class="col-md-10">
            <!-- Campo de texto para busca, mantendo o valor digitado -->
            <input type="text" class="form-control" name="search" placeholder="Pesquisar produtos..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <!-- Botão para enviar o formulário -->
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </form>

    <!-- Lista de produtos em grid responsiva -->
    <div class="row g-4">
    <?php if(count($produtos) === 0): ?>
        <!-- Mensagem caso nenhum produto seja encontrado -->
        <p class="text-center">Nenhum produto encontrado.</p>
    <?php else: ?>
        <?php foreach ($produtos as $produto): ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm d-flex flex-column">
                    <?php
                    // Verifica se o produto tem imagem, converte para base64 para mostrar inline
                    if (!empty($produto['imagem'])) {
                        $imgData = base64_encode($produto['imagem']);
                        $src = 'data:image/jpeg;base64,' . $imgData;
                    } else {
                        // Caso não tenha imagem, usa imagem placeholder
                        $src = 'https://via.placeholder.com/300x180?text=Sem+Imagem';
                    }
                    ?>
                    <!-- Imagem do produto -->
                    <img src="<?php echo $src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>" style="height: 180px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <!-- Nome do produto -->
                        <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                        <!-- Descrição do produto -->
                        <p class="card-text"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        <div class="mt-auto">
                            <!-- Preço do produto formatado -->
                            <strong class="text-success">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                            <!-- Formulário para adicionar 1 unidade ao carrinho -->
                            <form method="post" action="api/add_to_cart.php" class="mt-3 d-flex align-items-center gap-2">
                                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="btn btn-outline-primary btn-sm" title="Adicionar 1 unidade ao carrinho">
                                    <i class="bi bi-cart-plus"></i> Adicionar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>

</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
