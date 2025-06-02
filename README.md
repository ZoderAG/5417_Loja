══════════════════════════════════════════════════════════════════
                  PROJETO: 5417_LOJA ONLINE
══════════════════════════════════════════════════════════════════

📌 UFCD 5417 – Programação para a Web (Server-side)

══════════════════════════════════════════════════════════════════
🧩 FUNCIONALIDADES
══════════════════════════════════════════════════════════════════
- Autenticação de utilizadores (registo, login, ativação por email)
- Área de administração para gestão de produtos (criar, editar, eliminar)
- Listagem e pesquisa de produtos
- Carrinho de compras (adicionar, atualizar, remover)
- Finalização de compra com envio de email de confirmação
- Proteção de rotas (acesso limitado a utilizadores e administradores)

══════════════════════════════════════════════════════════════════
🛠️ TECNOLOGIAS UTILIZADAS
══════════════════════════════════════════════════════════════════
- PHP .................... Backend
- MySQL .................. Base de dados
- Bootstrap 5 ............ Frontend responsivo
- PHPMailer .............. Envio de emails (SMTP)

══════════════════════════════════════════════════════════════════
🚀 COMO EXECUTAR O PROJETO
══════════════════════════════════════════════════════════════════
1. Clonar o repositório e colocar os ficheiros num servidor local (ex: XAMPP)
2. Importar a base de dados através de um ficheiro .sql (não incluído)
3. Configurar a base de dados em:      api/db.php
4. Configurar as credenciais de email em:  api/secrets.php
5. Aceder ao ficheiro index.php via navegador

══════════════════════════════════════════════════════════════════
📁 ESTRUTURA DE FICHEIROS
══════════════════════════════════════════════════════════════════
index.php         → Página inicial com listagem/pesquisa de produtos  
api/              → Lógica de backend (autenticação, carrinho, emails, admin)  
views/            → Interfaces para login, registo, carrinho e administração

══════════════════════════════════════════════════════════════════
📝 CRÉDITOS
══════════════════════════════════════════════════════════════════
- PHPMailer (api/PHPMailer/README.md) – Envio de emails via SMTP

══════════════════════════════════════════════════════════════════
