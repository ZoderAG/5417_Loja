<?php
    // Redireciona o usuário para a página de login
    header("Location: login.php");

    // Encerra a sessão atual, removendo todos os dados da sessão
    session_destroy();

    // Encerra a execução do script para garantir que nada mais seja executado
    exit();
?>
