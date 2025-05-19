# Sistema de Gerenciamento de Consultas Odontológicas - Clínica Oral Care 🦷

Sistema web para gerenciamento de agendamentos odontológicos, desenvolvido como Trabalho de Conclusão de Curso (TCC) no curso de Engenharia de Software.

---

## 📋 Funcionalidades

- ✅ Cadastro de Pacientes
- 📅 Agendamento de Consultas com FullCalendar
- 🔄 Cancelamento e Reagendamento de Consultas
- 🦷 Registro de Procedimentos por Dentista
- 🕒 Gerenciamento de Horários por Especialista
- 📧 Envio de Lembretes por E-mail (SendGrid)
- 📁 Histórico do Paciente
- 🧾 Integração com Plano de Saúde (campo informativo)
- 🔐 Sistema de Login com controle de acesso (paciente, dentista, recepcionista)

---

## 🛠️ Tecnologias Utilizadas

- PHP 8.2
- MySQL
- Bootstrap 5
- JavaScript
- FullCalendar.js
- SendGrid API (via cURL e PHP)
- PHPMailer (opcional)
- Composer

---

## 🧱 Estrutura do Projeto

```bash
/clinica-oral-care/
├── index.php
├── login.php
├── cadastro_paciente.php
├── agendamento_consulta.php
├── reagendar_consulta.php
├── confirmar_consulta.php
├── registrar_procedimento.php
├── config.php             # ⚠️ Protegido por .gitignore
├── .env.example           # ✅ Exemplo de variáveis de ambiente
├── .gitignore             # Arquivos que não sobem ao Git
├── composer.json
├── /logs/                 # Logs de lembretes e envio
├── /vendor/               # Dependências (gerado por Composer)
└── README.md

⚙️ Instalação
1. Clone o repositório
git clone https://github.com/Hugopaulo1986/clinica-oral-care.git
cd clinica-oral-care

2. Instale as dependências
composer install

3.Crie o arquivo .env baseado no .env.example
SENDGRID_API_KEY=SUACHAVEAQUI
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_DATABASE=teste_db

4. Configure seu ambiente local
Use XAMPP ou WAMP

Crie o banco de dados MySQL e importe o script SQL

5. Acesse no navegador
http://localhost/clinica-oral-care

Segurança
Uso de session_start() para controle de sessão por perfil

Recomenda-se password_hash() e password_verify() no login

Chave do SendGrid carregada via .env

config.php, .env e logs protegidos por .gitignore

Repositório livre de segredos (sem chaves no histórico Git)

👨‍🎓 Autor
Hugo Manuel Rodrigues Paulo
Centro Universitário da Grande Dourados (UNIGRAN)
TCC de Engenharia de Software – 2025
Orientador: Prof. André Martins do Nascimento