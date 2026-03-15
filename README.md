## Pagamento Multi-Gateway

### Visão geral
API para processamento de pagamentos e gestão de transações/compras com fallback automático entre gateways. Ao criar uma compra, o sistema calcula o valor a partir de produto + quantidade, registra a transação como `pending` e tenta processar em gateways ativos por ordem de prioridade. Em falha, alterna para o próximo gateway; se todos falharem, a transação é marcada como `failed`. Baseado em api.php e lógica de fallback em TransactionService.php.

### Instalação e setup (passo a passo)
**Pré-requisitos**
- PHP 8.2, Composer, Node.js (Vite), Docker/Docker Compose.
- MySQL 8.0 (via Docker). Baseado em Dockerfile e docker-compose.yml.

**1) Clonar e instalar dependências**
```bash
git clone <URL_DO_REPOSITORIO>
cd gateway-pagamentos
composer install
npm install
```

**2) Configurar .env**
```bash
copy .env.example .env
```
Ajuste as variáveis (exemplo para Docker):
```
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

GATEWAY_AUTH_TOKEN=<defina um valor>
GATEWAY_AUTH_SECRET=<defina um valor>
```
- `GATEWAY_AUTH_TOKEN` e `GATEWAY_AUTH_SECRET` são usados no gateway com autenticação via header. Veja HeaderAuthGatewayService.php e .env.example.
- O gateway com bearer token autentica com credenciais fixas no serviço. Veja BearerTokenGatewayService.php.

**3) Subir com Docker**
```bash
docker compose up -d --build
```

**4) Gerar chave e migrar/seed**
```bash
php artisan key:generate
php artisan migrate --seed
```
Seeds criam usuários e perfis padrão (admin/manager/finance/user) e dados de apoio. Veja DatabaseSeeder.php.

**5) Rodar local (sem Docker)**
```bash
php artisan serve
npm run dev
```

### Autenticação
- **POST /login** retorna `token` (Laravel Sanctum).
- Para rotas privadas, enviar `Authorization: Bearer <token>`.
Baseado em AuthService.php.

### Documentação de API (principais endpoints)

**Públicos**
- **POST /login**
  - Body: `email`, `password`
  - Respostas: `200` token, `422` credenciais inválidas.
  - Validação: AuthRequest.php

- **POST /purchases**
  - Body:  
    `client_id`, `product_id`, `quantity`, `payment_method` (`card_credit|card_debit`),  
    `card_number` (16 dígitos), `cvv` (3 chars), `name`, `email`
  - Respostas: `201` criado, `422` validação, `400/500` falha no gateway.
  - Validação: TransactionStoreRequest.php

**Privados (auth:sanctum + roles)**

**Gateways (ADMIN)**
- **PATCH /gateways/{id}/activate**
- **PATCH /gateways/{id}/deactivate**
- **PATCH /gateways/{id}/priority**  
  Body: `priority` (0–100)  
  Respostas: `200`, `403`, `422`  
  Validações: GatewayActivateAndDeactivateRequest.php,  
  GatewayUpdatePriorityRequest.php

**Usuários (MANAGER, ADMIN)**
- **GET /users**
- **POST /users**  
  Body: `name`, `email`, `password`, `role_id`  
- **PATCH /users/{id}**  
  Body: `name?`, `email?`, `password?`, `role_id?`  
- **DELETE /users/{id}`**  
  Respostas: `200`, `403`, `422`  
  Validações: UserStoreRequest.php,  
  UserUpdateRequest.php

**Produtos (MANAGER, FINANCE, ADMIN)**
- **GET /products**
- **POST /products**  
  Body: `name`, `amount` (int, em centavos)
- **PATCH /products/{id}`**  
  Body: `name?`, `amount?`
- **DELETE /products/{id}`**  
  Respostas: `200`, `403`, `422`  
  Validações: ProductStoreRequest.php,  
  ProductUpdateRequest.php

**Clientes (MANAGER, FINANCE, ADMIN)**
- **GET /clients**
- **GET /clients/{id}`**  
  Respostas: `200`, `403`, `422`  
  Validações: ClientIdRequest.php

**Compras (FINANCE, ADMIN)**
- **GET /purchases**
- **GET /purchases/{id}`**
- **POST /purchases/{id}/refund**  
  Respostas: `200`, `403`, `422`, `500`  
  Validação: TransactionRefundRequest.php

### Segurança e roles
Controle de acesso via middleware `CheckRole`:
- `ADMIN`: acesso total às rotas privadas.
- `MANAGER`: gerencia usuários e produtos; acessa clientes.
- `FINANCE`: gerencia produtos; acessa clientes, compras e reembolsos.
- `USER`: apenas rotas públicas.  
Baseado em CheckRole.php e regras definidas em api.php.

### Diferenciais técnicos
- **Arquitetura em camadas** com Controllers → Services → Repositories, separando regra de negócio e acesso a dados. Exemplos em Services e Repositories.
- **Fallback multi-gateway por prioridade**: o pagamento é tentado em múltiplos gateways ativos, com failover automático e atualização de status. Veja TransactionService.php.
- **Reembolsos consistentes**: valida status da transação, chama o gateway correto e atualiza compra/transação. Veja TransactionService.php e PurchaseRepository.php.
- **Validações fortes de request** com Form Requests, garantindo payloads corretos e respostas 422 para dados inválidos.

Se quiser, posso entregar a mesma estrutura já aplicada dentro do README.md ou como corpo de PR com formatação específica.