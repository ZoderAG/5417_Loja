<?php

require 'db.php';     // Inclui o arquivo de conexão com o banco de dados
require 'email.php';  // Inclui arquivo com função para envio de email

/**
 * Realiza o login de um utilizador verificando username/email e password
 * 
 * @param string $userinput -> Nome de utilizador ou email
 * @param string $password  -> Password fornecida pelo utilizador
 * @return bool             -> true se login bem sucedido, false caso contrário
 */
function login($userinput, $password){  
    global $con;

    // Prepara consulta para buscar utilizador pelo username ou email e que esteja ativo
    $sql = $con->prepare("SELECT * FROM Utilizador WHERE (username = ? OR email = ?) AND active = 1");
    $sql->bind_param('ss', $userinput, $userinput);
    $sql->execute();
    $result = $sql->get_result();

    if($result->num_rows > 0){
        // Utilizador encontrado, pega os dados
        $row = $result->fetch_assoc();

        // Verifica se a password fornecida confere com a password hasheada no BD
        if(password_verify($password, $row["password"])) {
            // Se a password está correta, guarda os dados do utilizador na sessão
            $_SESSION["user"] = $row;
            return true;
        }
    }
    // Caso não encontre o utilizador ou a password esteja errada, retorna false
    return false;
}

/** 
 * Regista um novo utilizador na base de dados
 * 
 * @param string $email     -> Email do utilizador
 * @param string $username  -> Nome de utilizador
 * @param string $password  -> Password do utilizador
 * @param string $telemovel -> Número de telemóvel
 * @param string $nif       -> Número de Identificação Fiscal
 * @return bool             -> true se o registo foi bem sucedido, false caso contrário
 */
function registo($email, $username, $password, $telemovel, $nif){
    global $con;

    // 1º - Prepara a query para inserir novo utilizador (RoleID = 2 = utilizador padrão)
    $sql = $con->prepare('INSERT INTO Utilizador(email, username, password, telemovel, nif, token, RoleID) VALUES (?, ?, ?, ?, ?, ?, 2)');
    
    // 2º - Gera um token aleatório para ativação da conta (32 caracteres hexadecimais)
    $token = bin2hex(random_bytes(16));
    
    // 3º - Encripta a password usando o algoritmo padrão (bcrypt)
    $password = password_hash($password, PASSWORD_DEFAULT);
    
    // 4º - Liga os parâmetros da query aos valores recebidos
    $sql->bind_param('ssssss', $email, $username, $password, $telemovel, $nif, $token);
    
    // Executa a query para inserir o utilizador
    $sql->execute();
    
    if($sql->affected_rows > 0){
        // 5º - Se o registo foi bem sucedido, envia email com link para ativação da conta
        send_email(
            $email,
            'Ativar a conta',
            "<a href='localhost/24198_Loja/views/ativarconta.php?email=$email&token=$token'>Ative a sua conta</a>"
        );
        return true;
    } else {
        // Se falhou o insert, retorna false
        return false;
    }
}

/**
 * Ativa a conta de um utilizador com base no email e token enviados no link do email
 * 
 * @param string $email -> Email do utilizador
 * @param string $token -> Token de ativação enviado no email
 * @return bool        -> true se ativação bem sucedida, false caso contrário
 */
function ativarConta($email, $token){
    global $con;

    // Atualiza o campo active para 1 (ativo) se o email e token conferem
    $sql = $con->prepare("UPDATE Utilizador SET active = 1 WHERE email = ? AND token = ?");
    $sql->bind_param('ss', $email, $token);
    $sql->execute();

    // Retorna true se alguma linha foi afetada (ou seja, conta ativada)
    return ($sql->affected_rows > 0);
}

// Função logout vazia, pode ser implementada para limpar a sessão do utilizador
function logout(){
    // Implementar destruição da sessão, unset das variáveis e redirecionamento
}

// Função apagarConta vazia, pode ser implementada para deletar utilizador da base de dados
function apagarConta(){
    // Implementar lógica para apagar conta do utilizador
}

/**
 * Verifica se o utilizador logado é administrador
 * 
 * @return bool -> true se for admin, false caso contrário
 */
function isAdmin(){
    // Assume que RoleID = 1 corresponde a administrador
    return (isset($_SESSION["user"]["RoleID"]) && $_SESSION["user"]["RoleID"] == 1);
}

?>
