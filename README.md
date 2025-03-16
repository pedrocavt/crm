# 🚀 CRM de Agendamento de Reuniões

Este é um **CRM simples para agendamento de reuniões**, desenvolvido utilizando **Laravel** no backend e **Vue.js** no frontend. O sistema permite o gerenciamento de usuários, criação de reuniões e notificações em tempo real com **Pusher**.

---

## 📂 **Estrutura do Projeto**

O projeto está organizado da seguinte forma:

crm-ricochet/ 
│── backend/ # Código fonte do backend (Laravel) 
│── frontend/ # Código fonte do frontend (Vue.js) 
│── docker/ # Configuração dos Dockerfiles para backend e frontend 
│── docker-compose.yml # Arquivo de configuração do Docker Compose 
│── install.sh # Script para instalação e configuração do projeto 
│── README.md # Documentação do projeto

---

## 🛠 **Tecnologias Utilizadas**

### **Backend (Laravel)**
- PHP 8.2 + Laravel 12
- MySQL 8
- PHPUnit (Testes)
- JWT para autenticação
- Laravel Broadcasting (Pusher) para notificações em tempo real
- Laravel Queue para processamento assíncrono

### **Frontend (Vue.js)**
- Vue 3 + Vite
- Vue Router + Pinia
- Tailwind CSS para estilização
- Axios para chamadas à API

### **Infraestrutura**
- **Docker** + Docker Compose
- **Nginx** como servidor web
- **MySQL** como banco de dados

---

## 🚀 **Como Instalar e Rodar o Projeto**

### **📌 Pré-requisitos**
Antes de começar, certifique-se de ter instalado:
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### **📌 1. Clonar o repositório**
```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio