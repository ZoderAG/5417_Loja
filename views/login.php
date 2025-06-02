<?php
// Inicia a sessão para poder guardar dados do utilizador durante a navegação
session_start();

// Inclui o arquivo que contém a função login(), responsável por autenticar o utilizador
require "../api/auth.php";

// Inicializa as variáveis de controle de erro e mensagem
$error = false;
$message = "";

// Verifica se o formulário foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Remove espaços em branco no início e fim dos campos enviados
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Valida se os campos foram preenchidos
    if (!$username || !$password) {
        $error = true; // Marca erro para exibir mensagem
        $message = "Por favor, preencha todos os campos.";
    }
    // Verifica se as credenciais são válidas usando a função login()
    elseif (!login($username, $password)) {
        $error = true;
        $message = "Credenciais inválidas. Tente novamente.";
    }
    // Se login for válido, redireciona para a página principal do sistema
    else {
        header("Location: ../index.php");
        exit(); // Para a execução do script
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8"> <!-- Define a codificação de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Login | Sistema</title> <!-- Título da página -->

    <!-- Link para o CSS do Bootstrap (biblioteca de estilos prontos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Estiliza o corpo com um gradiente de fundo e fonte */
        body {
            background: linear-gradient(135deg, #e3f2fd, rgb(0, 0, 0));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Estilo do card branco onde está o formulário */
        .login-card {
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            padding: 2rem;
        }

        /* Efeito hover: levanta o card e aumenta a sombra */
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        }

        /* Estilo dos campos de formulário */
        .form-control {
            border: 1px solid #ced4da; /* borda cinza clara */
            border-radius: 20px; /* bordas arredondadas */
            padding: 1rem;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        /* Quando o input estiver focado (clicado) */
        .form-control:focus {
            border-color: #0d6efd; /* borda azul */
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); /* sombra azul */
        }

        /* Botão principal */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 0.75rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        /* Botão muda cor quando mouse passa por cima */
        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        /* Estilo do alerta de erro */
        .alert {
            font-size: 0.95rem;
            border-radius: 15px;
        }

        /* Estilo do título do formulário */
        h2 {
            font-weight: 600;
            color: #343a40;
        }

        /* Ajustes para telas pequenas */
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <!-- Centraliza o conteúdo vertical e horizontalmente -->

    <main class="container" style="max-width: 400px;">
        <div class="card login-card bg-white border-0">
            <!-- Título do formulário -->
            <h2 class="text-center mb-4">Área de Login</h2>

            <!-- Se houver erro, mostra o alerta com a mensagem -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?> <!-- Escapa caracteres especiais para segurança -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <!-- Formulário de login -->
            <form method="POST" novalidate>
                <div class="form-floating mb-3">
                    <input type="text" name="username" id="username" class="form-control" placeholder="Utilizador" required autofocus>
                    <label for="username">Utilizador</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Senha" required>
                    <label for="password">Senha</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Script do Bootstrap para componentes funcionarem (ex: alerta fechável) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
