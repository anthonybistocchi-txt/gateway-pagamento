# Pagamento Multi-Gateway 

## 1) Visao Geral e Funcionalidades
Este projeto implementa uma funcionalidade de Pagamento multi-gateway. Ao criar uma compra, o sistema calcula o valor com base no produto e na quantidade e tenta processar o pagamento em gateways ativos seguindo a prioridade configurada. Se um gateway falhar, o proximo e tentado.

### Principais rotas (API)
**Publicas**
- `POST /login` autentica e retorna token
- `POST /purchases` cria uma compra

**Privadas (auth:sanctum)**
- Gateways (ADMIN)
	- `PATCH /gateways/{id}/activate`
	- `PATCH /gateways/{id}/deactivate`
	- `PATCH /gateways/{id}/priority`
- Usuarios (MANAGER, ADMIN)
	- `GET /users`
	- `POST /users`
	- `PATCH /users/{id}`
	- `DELETE /users/{id}`
- Produtos (MANAGER, FINANCE, ADMIN)
	- `GET /products`
	- `POST /products`
	- `PATCH /products/{id}`
	- `DELETE /products/{id}`
- Clientes (MANAGER, FINANCE, ADMIN)
	- `GET /clients`
	- `GET /clients/{id}`
- Compras (FINANCE, ADMIN)
	- `GET /purchases`
	- `GET /purchases/{id}`
	- `POST /purchases/{id}/refund`

### Regra de negocio do Middleware de Permissoes
O middleware `CheckRole` valida o papel do usuario autenticado e permite ou bloqueia o acesso:
- ADMIN: acesso total a rotas privadas
- MANAGER: gerencia usuarios e produtos; acessa clientes
- FINANCE: gerencia produtos; acessa clientes, compras e reembolsos
- USER: apenas rotas publicas

## 2) Pre-requisitos
- PHP 8.2 (composer.json)
- Node.js (necessario para Vite/Tailwind; use versao compativel com Vite 7)
- Docker e Docker Compose (docker-compose.yml)
- MySQL 8.0 (imagem do docker-compose)

## 3) Guia de Instalacao Passo a Passo

### Clonar o repositorio
```bash
git clone <URL_DO_REPOSITORIO>
cd payment-gateway
```

### Instalar dependencias
```bash
composer install
npm install
```

### Configurar .env
Crie o arquivo `.env` a partir do `.env.example` e ajuste as variaveis criticas.

**Banco de dados (MySQL)**
- `DB_CONNECTION=mysql`
- `DB_HOST=` (Docker) ou `127.0.0.1` (local)
- `DB_PORT=3306`
- `DB_DATABASE=`
- `DB_USERNAME=`
- `DB_PASSWORD=`

**Gateway de Pagamento**
- `GATEWAY_AUTH_TOKEN`
- `GATEWAY_AUTH_SECRET`

**Aplicacao**
- `APP_KEY` (gerado via artisan)
- `APP_URL=http://localhost:8000`

### Subir com Docker (recomendado)
```bash
docker compose up -d --build
```

### Migrar e popular o banco
```bash
php artisan key:generate
php artisan migrate --seed
```

Se quiser rodar apenas os seeds:
```bash
php artisan db:seed
```

### Rodar localmente (sem Docker)
```bash
php artisan serve
npm run dev
```

## 4) Stack Tecnologica
- Laravel 12, PHP 8.2, Sanctum
- MySQL 8.0
- Docker e Docker Compose
