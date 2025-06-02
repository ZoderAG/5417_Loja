<?php
// Inclui o arquivo com as funções de autenticação e registro
require "../api/auth.php";

// Variáveis para controlar erros e mensagens
$error_msg = false;
$msg = "";

// Verifica se o formulário foi enviado via método POST e se todos os campos esperados estão definidos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["email"], $_POST["telemovel"], $_POST["nif"], $_POST["password"], $_POST["confirm_password"])) {

    // Validação básica para verificar se cada campo foi preenchido
    if (empty($_POST["username"])) {
        $error_msg = true;
        $msg .= "Preencha o campo username. ";
    }
    if (empty($_POST["email"])) {
        $error_msg = true;
        $msg .= "Preencha o campo email. ";
    }
    if (empty($_POST["telemovel"])) {
        $error_msg = true;
        $msg .= "Preencha o campo telemóvel. ";
    }
    if (empty($_POST["nif"])) {
        $error_msg = true;
        $msg .= "Preencha o campo NIF. ";
    }
    if (empty($_POST["password"])) {
        $error_msg = true;
        $msg .= "Preencha o campo password. ";
    }
    if (empty($_POST["confirm_password"])) {
        $error_msg = true;
        $msg .= "Preencha o campo confirmar password. ";
    }

    // Verifica se as senhas coincidem
    if ($_POST["password"] != $_POST["confirm_password"]) {
        $error_msg = true;
        $msg .= "As passwords não coincidem. ";
    }

    // Se não houver erros, tenta registrar o utilizador
    if (!$error_msg) {
        // Função registo() deve validar e inserir o utilizador no banco
        if (registo($_POST["email"], $_POST["username"], $_POST["password"], $_POST["telemovel"], $_POST["nif"])) {
            // Se registro for bem-sucedido, redireciona para página de login
            header("Location: login.php");
            exit;
        } else {
            // Caso contrário, mostra mensagem de erro genérica
            $error_msg = true;
            $msg = "O registo falhou. Verifique os seus dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo | Sistema</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Estilos gerais da página */
        body {
            background: linear-gradient(135deg, #e3f2fd,rgb(0, 0, 0));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Estilo do cartão do formulário */
        .register-card {
            border-radius: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            background-color: white;
        }

        /* Efeito hover para o cartão */
        .register-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        }

        /* Estilos dos inputs do formulário */
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 20px;
            padding: 1rem;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        /* Estilo do input quando focado */
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Estilo do botão principal */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 0.75rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        /* Hover do botão */
        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        /* Estilo do alerta de erro */
        .alert {
            font-size: 0.95rem;
            border-radius: 15px;
        }

        /* Título */
        h2 {
            font-weight: 600;
            color: #343a40;
        }

        /* Ajuste para telas pequenas */
        @media (max-width: 576px) {
            .register-card {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

<!-- Container centralizado com largura máxima -->
<main class="container" style="max-width: 500px;">
    <div class="register-card">
        <h2 class="text-center mb-4">Registo</h2>

        <!-- Exibe mensagem de erro caso haja -->
        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <!-- Formulário de registro -->
        <form method="POST" novalidate>
            <div class="form-floating mb-3">
                <input type="text" name="username" id="username" class="form-control" placeholder="Nome de utilizador" required>
                <label for="username">Nome de utilizador</label>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                <label for="email">Email</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="telemovel" id="telemovel" class="form-control" placeholder="Telemóvel" required>
                <label for="telemovel">Telemóvel</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="nif" id="nif" class="form-control" placeholder="NIF" required>
                <label for="nif">NIF</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirmar Password" required>
                <label for="confirm_password">Confirmar Password</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Registar</button>
            </div>
        </form>
    </div>
</main>

<!-- Scripts do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
