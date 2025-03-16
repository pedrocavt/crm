#!/bin/bash

echo "Iniciando a instalação do projeto..."

# Subir os containers
docker-compose up -d --build \
&& echo "Copiando .env.example para .env" \
&& cp backend/.env.example backend/.env \
&& echo "Instalando dependências do backend..." \
&& docker exec crm_app composer install \
&& echo "Gerando chave do Laravel..." \
&& docker exec crm_app php artisan key:generate \
&& echo "Executando migrations e seeders..." \
&& docker exec crm_app php artisan migrate --seed \
&& echo "Ajustando permissões..." \
&& docker exec crm_app chmod -R 777 storage bootstrap/cache

# Rodar workers e agendador em background
docker exec crm_app php artisan schedule:work & \
docker exec crm_app php artisan queue:work &

# Configuração do frontend
echo "Instalando dependências do frontend..."
docker exec crm_vue_frontend npm install \
&& echo "Iniciando frontend..." \
&& docker exec crm_vue_frontend npm run dev --host &

echo "Setup concluído! O backend está rodando em http://localhost:8000 e o frontend em http://localhost:5173"
