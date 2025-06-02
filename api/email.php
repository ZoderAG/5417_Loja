<?php

// Usa namespaces da biblioteca PHPMailer para facilitar o uso das classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Função para enviar email usando PHPMailer via SMTP
 * 
 * @param string $to      -> Destinatário do email
 * @param string $subject -> Assunto do email
 * @param string $message -> Corpo do email (HTML)
 * @return bool           -> true se email enviado com sucesso, false caso contrário
 */
function send_email($to, $subject, $message) {
    
    // Inclui os arquivos necessários da biblioteca PHPMailer
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    
    // Inclui arquivo que contém as variáveis secretas (usuário e senha SMTP)
    require "secrets.php";

    // Cria nova instância do PHPMailer, passando true para habilitar exceções
    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = "utf-8";  // Define charset para evitar problemas de acentuação
        
        // Configurações do servidor SMTP
        $mail->isSMTP();                    // Define que usará SMTP para envio
        $mail->Host       = 'smtp.sapo.pt'; // Servidor SMTP do SAPO
        $mail->SMTPAuth   = true;           // Ativa autenticação SMTP
        $mail->Username   = $EMAIL_SAPO;    // Usuário SMTP (definido em secrets.php)
        $mail->Password   = $EMAIL_PASS;    // Senha SMTP (definida em secrets.php)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usa criptografia TLS implícita
        $mail->Port       = 465;            // Porta padrão para TLS implícito
        
        // Configurações do remetente e destinatário
        $mail->setFrom($EMAIL_SAPO, "App Loja 24198"); // Email e nome do remetente
        $mail->addAddress($to, $to);       // Adiciona o destinatário (email e nome iguais)
        
        // Conteúdo do email
        $mail->isHTML(true);               // Define que o corpo será HTML
        $mail->Subject = $subject;        // Assunto do email
        $mail->Body    = $message;        // Corpo do email (HTML)
        
        $mail->send();                    // Tenta enviar o email
        
        return true;                      // Retorna true se enviado com sucesso
    } catch (Exception $e) {
        // Se houver exceção/erro no envio, retorna false
        return false;
    }
}

?>
