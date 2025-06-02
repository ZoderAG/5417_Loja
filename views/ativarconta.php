<?php
// Inclui o arquivo que contém as funções de autenticação
require "../api/auth.php";

// Verifica se os parâmetros 'email' e 'token' foram passados via URL (método GET)
if(isset($_GET["email"]) && isset($_GET["token"])) {
    // Chama a função ativarConta passando o email e token para ativar a conta do utilizador
    ativarConta($_GET["email"], $_GET["token"]);

    // Redireciona o utilizador para a página de login após ativar a conta
    header("Location: login.php");
    exit(); // Encerra o script para garantir que o redirecionamento ocorra imediatamente
}

?>
