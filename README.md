# API de Pagamento Multi-Gateway

**Versão atual: v1.1** — aprimoramento da base anterior (v1.0); mesma arquitetura e fluxos principais, com idempotência via DTOs, validação reforçada e suíte de testes automatizados.

> **Visão geral:** API para processamento de pagamentos e gestão de transações/compras com *fallback* automático entre gateways. Ao criar uma compra, o sistema calcula o valor total (produto × quantidade), registra a transação e tenta processá-la nos gateways ativos por prioridade. Se um provedor falhar, o próximo é tentado. Se todos falharem, a transação é marcada como falha de forma segura.

Este projeto atende ao nível 2 do teste prático da BeTalent Tech.

---

## O que há de novo na v1.1

A **v1.1** não substitui o desenho da v1.0: ela **refina** o mesmo produto com três focos:

1. **Idempotência de criação de transação** — o cliente envia um `payment_key` (UUID) por tentativa de pagamento; o backend evita cobranças duplicadas e responde de forma previsível em reenvios de rede ou cliques repetidos.
2. **DTOs (`StoreTransactionResult` e `RefundTransactionResult`)** — o serviço de transação devolve sempre um objeto com **código HTTP** e **corpo JSON** alinhados ao caso (criado, replay idempotente, conflito pendente, reembolso), em vez de espalhar `response()->json()` com números mágicos pelo controller.
3. **Testes automatizados** — cobertura de validação, fluxo feliz, *fallback* entre gateways, idempotência e conflito de `payment_key`, além de testes unitários nos DTOs.

Se você já conhecia a versão anterior, pense na v1.1 como **evolução incremental**: mesma stack (Laravel 12, Sanctum, camadas Controller → Service → Repository), com comportamento de API mais seguro para integrações e regressões cobertas por testes.

---

## Idempotência e DTOs (v1.1)

### `payment_key`

- Enviado no **POST** de criação de transação (`/api/purchases`).
- Deve ser um **UUID** válido, único por **intenção de pagamento** (ex.: gerado no front ou no cliente que chama a API).
- O par **cliente + `payment_key`** identifica de forma estável uma mesma tentativa de compra.

### `StoreTransactionResult`

Classe **readonly** que centraliza a resposta da criação:

| Situação | HTTP | Ideia |
|----------|------|--------|
| Nova transação concluída com sucesso | **201** | `created()` — pagamento processado e persistido. |
| Já existe transação para esse `payment_key` (ex.: já **completed**, **failed**, **refunded**) | **200** | `idempotentReplay()` — não reprocessa o cartão; informa o estado atual (mensagem varia conforme o status). |
| Já existe transação **pending** para o mesmo `payment_key` | **409** | `pendingConflict()` — outra requisição ainda está em andamento; evita corrida duplicada. |

O `TransactionController` apenas faz `response()->json($result->payload, $result->httpStatus)`, mantendo a camada HTTP fina.

### `RefundTransactionResult`

DTO análogo para o fluxo de estorno (`success()` com payload e status HTTP consistentes).

### Observação de concorrência

Em condição de corrida no banco (duas requisições criando ao mesmo tempo), o serviço trata violação de unicidade em `payment_key` e converge para o mesmo tipo de resposta idempotente ou de conflito, conforme o estado da transação já persistida.

---

## Testes automatizados

A suíte usa **PHPUnit** via `php artisan test` (configuração em `phpunit.xml`, banco **SQLite em memória** no ambiente de teste).

### Tipos de teste

| Tipo | O que valida | Onde |
|------|----------------|------|
| **Unitário** | Comportamento isolado, sem HTTP nem banco (ex.: estrutura dos DTOs). | `tests/Unit/` |
| **Feature** | Requisições HTTP, validação, persistência e `Http::fake()` para gateways externos. | `tests/Feature/` |

### Cenários principais cobertos (exemplos)

- Login (credenciais válidas e inválidas).
- **POST /api/purchases**: validação de `payment_key`, formato UUID, existência de `client_id`.
- Sucesso no gateway de maior prioridade; **fallback** quando o primeiro gateway falha.
- **Idempotência**: segundo POST com o mesmo `payment_key` após sucesso → **200** com mensagem de transação já concluída.
- **Conflito**: transação ainda **pending** para o mesmo `payment_key` → **409**.
- Health check (`GET /up`).

### Como executar

```bash
composer test
# ou
php artisan test
```

Variáveis de gateway de teste estão definidas em `phpunit.xml` para que `Http::fake()` funcione de forma determinística.

---

## ⚙️ Pré-requisitos

- PHP 8.2+
- Composer
- Node.js (Vite)
- Docker e Docker Compose (para MySQL 8.0, se usar o stack com containers)

---

## 🛠️ Instalação e setup

**1. Clone o repositório e instale as dependências**

```bash
git clone <URL_DO_REPOSITORIO>
cd gateway-pagamentos
composer install
npm install
```

**2. Configurar `.env`**

```bash
copy .env.example .env
```

Ajuste as variáveis (exemplo para Docker):

```env
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

- `GATEWAY_HEADER_TOKEN` e `GATEWAY_HEADER_SECRET` são usados no gateway com autenticação via header (ver `HeaderAuthGatewayService.php` e `.env.example`).
- O gateway Bearer autentica com credenciais configuradas no serviço (ver `BearerTokenGatewayService.php`).

**3. Subir com Docker**

```bash
docker compose up -d --build
```

**4. Gerar chave e migrar/seed**

```bash
php artisan key:generate
php artisan migrate --seed
```

Os seeders criam usuários e perfis padrão (admin/manager/finance/user) e dados de apoio. Ver `DatabaseSeeder.php`.

**5. Rodar local (sem Docker)**

```bash
php artisan serve
npm run dev
```

### Autenticação

- **POST /login** retorna `token` (Laravel Sanctum).
- Para rotas privadas, enviar `Authorization: Bearer <token>`.

---

## 📡 Documentação da API (endpoints)

### 🔓 Rotas públicas

**POST /login**

- Body: `email`, `password`
- Respostas: `200` (token) | `401` / `422` (credenciais inválidas)

**POST /api/purchases** (criação de transação / processamento de compra)

- Body: `payment_key` (UUID, obrigatório na v1.1), `client_id`, `product_id`, `quantity`, `payment_method` (`card_credit` / `card_debit`), `card_number`, `cvv`, `name`, `email`
- Respostas: `201` (nova transação concluída) | `200` (replay idempotente) | `409` (conflito com transação pendente no mesmo `payment_key`) | `422` (validação) | `400` / `500` (falha nos gateways)

> O CVV é usado apenas em memória para a integração externa e não é persistido (alinhado a boas práticas de dados de cartão).

### 🔒 Rotas privadas (autenticação obrigatória)

**Gateways (ADMIN)**

- `PATCH /api/gateways/{id}/activate` — ativa um gateway.
- `PATCH /api/gateways/{id}/deactivate` — desativa um gateway.
- `PATCH /api/gateways/{id}/priority` — altera prioridade (swap atômico no banco). Body: `priority`.

**Usuários (ADMIN, MANAGER)**

- `GET/POST/PATCH/DELETE /api/users` — CRUD conforme `api.php`.

**Produtos (ADMIN, MANAGER, FINANCE)**

- `GET/POST/PATCH/DELETE /api/products` — CRUD de produtos (`amount` conforme regra do projeto).

**Clientes (ADMIN, MANAGER, FINANCE)**

- `GET /api/clients`, `GET /api/clients/{id}`

**Compras (FINANCE)**

- `GET /api/purchases` — histórico.
- `GET /api/purchases/{id}` — detalhes (compra, transação, cliente).
- `POST /api/purchases/{id}/refund` — estorno no gateway e atualização atômica no banco.

### Segurança e perfis

Controle de acesso via middleware `CheckRole`:

- **ADMIN:** acesso amplo às rotas de administração de gateways.
- **MANAGER:** usuários, produtos, clientes.
- **FINANCE:** produtos, clientes, compras e reembolsos.
- **USER:** em geral apenas contexto público; detalhes em `CheckRole` e `routes/api.php`.

### Diferenciais técnicos

- **Arquitetura em camadas:** Controllers, Services e Repositories, separando regra de negócio de acesso a dados (Eloquent).
- **Gateways plugáveis:** interfaces para serviços de pagamento, facilitando novos provedores sem alterar o núcleo.
- **Transações ACID:** `DB::transaction` em operações críticas (compra, estorno, troca de prioridade).
- **Validação:** Form Requests com respostas `422` padronizadas.
- **Configuração:** credenciais e URLs de gateway via ambiente; dados sensíveis de cartão não persistidos além do necessário.

---

*Documentação alinhada à v1.1 — aprimoramento da versão base com idempotência, DTOs de resposta e testes.*
