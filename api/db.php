<?php

// Ativa o relatório de erros do MySQLi para que erros sejam lançados como exceções
mysqli_report(MYSQLI_REPORT_ERROR);

// Cria uma nova conexão MySQLi com o servidor local, usuário root, sem senha, e banco de dados '24198_Loja'
$con = new mysqli("localhost", "root", "", "24198_Loja");

// Verifica se houve erro na conexão
if ($con->connect_error) {
    // Se ocorrer erro, termina o script exibindo mensagem de erro detalhada
    die("connection failed: " . $con->connect_error);
}

?>
