# API de Pagamento Multi-Gateway

> **Visão Geral:** API robusta para processamento de pagamentos e gestão de transações/compras com sistema inteligente de *fallback* automático entre gateways. 
> 
> Ao criar uma compra, o sistema calcula o valor total (produto + quantidade), registra a transação como pendente e tenta processá-la nos gateways ativos respeitando a ordem de prioridade. Em caso de falha de um provedor, o sistema alterna automaticamente para o próximo gateway disponível. Se todos falharem, a transação é atualizada com segurança para o status de falha.

Este projeto atende ao nivel 2 do teste prático da BeTalent Tech.
---

## ⚙️ Pré-requisitos

Certifique-se de ter os seguintes requisitos instalados em sua máquina:
* PHP 8.2+
* Composer
* Node.js (Vite)
* Docker e Docker Compose (para o banco de dados MySQL 8.0)

---

## 🛠️ Instalação e Setup (Passo a Passo)

**1. Clone o repositório e instale as dependências**
```bash
git clone <URL_DO_REPOSITORIO>
cd gateway-pagamentos
composer install
npm install

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
DB_DATABASE=betalent_db
DB_USERNAME=betalent_user
DB_PASSWORD=secret

# Credenciais do Gateway 1 (Bearer Token)
GATEWAY_BEARER_URL=http://gateways-mock:3001
GATEWAY_BEARER_EMAIL=dev@betalent.tech
GATEWAY_BEARER_TOKEN=FEC9BB078BF338F464F96B48089EB498

# Credenciais do Gateway 2 (Header Auth)
GATEWAY_HEADER_URL=http://gateways-mock:3002
GATEWAY_HEADER_TOKEN=seu_token_aqui
GATEWAY_HEADER_SECRET=seu_secret_aqui
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

📡 Documentação da API (Endpoints)
🔓 Rotas Públicas
POST /login

Body: email, password

Respostas: 200 (Retorna o Token) | 422 (Credenciais inválidas)

POST /purchases (Processamento de Compra)

Body: client_id, product_id, quantity, payment_method (card_credit/card_debit), card_number, cvv, name, email

Respostas: 201 (Sucesso) | 422 (Erro de Validação) | 400/500 (Falha nos Gateways)

Nota: O CVV trafega apenas em memória para a integração externa e não é persistido no banco de dados (Compliance PCI-DSS).

🔒 Rotas Privadas (Requer Autenticação)
⚙️ Gateways (Apenas ADMIN)

PATCH /gateways/{id}/activate - Ativa um gateway.

PATCH /gateways/{id}/deactivate - Desativa um gateway.

PATCH /gateways/{id}/priority - Altera a prioridade (Executa Swap atômico no banco).

Body: priority (0-100)

👥 Usuários (ADMIN, MANAGER)

GET /users - Lista usuários.

POST /users - Cria usuário (name, email, password, role_id).

PATCH /users/{id} - Atualiza dados do usuário.

DELETE /users/{id} - Remove usuário.

📦 Produtos (ADMIN, MANAGER, FINANCE)

GET /products - Lista produtos.

POST /products - Cria produto (name, amount em centavos).

PATCH /products/{id} - Atualiza produto.

DELETE /products/{id} - Remove produto.

🤝 Clientes (ADMIN, MANAGER, FINANCE)

GET /clients - Lista clientes.

GET /clients/{id} - Detalhes de um cliente específico.

💳 Compras (ADMIN, FINANCE)

GET /purchases - Lista histórico de compras.

GET /purchases/{id} - Detalhes com Nested Relationship (Compra + Transação + Cliente).

POST /purchases/{id}/refund - Executa estorno na API do Gateway e atualiza o banco local de forma atômica.

### Segurança e roles
Controle de acesso via middleware `CheckRole`:
- `ADMIN`: acesso total às rotas privadas.
- `MANAGER`: gerencia usuários e produtos; acessa clientes.
- `FINANCE`: gerencia produtos; acessa clientes, compras e reembolsos.
- `USER`: apenas rotas públicas.  
Baseado em CheckRole.php e regras definidas em api.php.

### Diferenciais técnicos
Arquitetura em Camadas (Clean Code): Utilização robusta de Controllers, Services e Repositories, garantindo a separação absoluta entre regras de negócio e manipulação de dados (Eloquent).

Integração Externa Componentizada: Implementação de contratos (Interfaces) para os serviços de Gateway, facilitando a troca e adição de novos provedores de pagamento sem alterar o core da aplicação.

Transações Atômicas (ACID): Uso intensivo de DB::transaction em operações críticas (Criação de Compras, Estornos e Swap de Prioridades), garantindo integridade financeira e prevenindo inconsistências de dados.

Validação Blindada: Uso de Form Requests personalizados em todas as entradas de dados, retornando erros 422 padronizados de forma automática.

Segurança Reforçada: Extração de credenciais hardcoded para variáveis de ambiente e exclusão de dados sensíveis de pagamento da camada de persistência.
