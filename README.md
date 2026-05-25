# Employee App

Employee management app with OCR-driven license plate / NIK capture.

## Architecture

This project follows **Clean / Hexagonal Architecture** with four distinct layers.

```
┌────────────────────────────────────────────────────────────────┐
│  Interface (HTTP)          app/Http/                           │
│  Thin controllers, FormRequests, API Resources                 │
├────────────────────────────────────────────────────────────────┤
│  Application               app/Application/                    │
│  Use cases (ListEmployees, GetEmployee, Create, Update, Delete)│
│  DTOs (EmployeeListCriteria, CreateEmployeeData, …)            │
├────────────────────────────────────────────────────────────────┤
│  Domain                    app/Domain/                         │
│  EmployeeRepositoryInterface, EmployeeNotFoundException        │
│  No framework dependencies — pure PHP contracts                │
├────────────────────────────────────────────────────────────────┤
│  Infrastructure            app/Infrastructure/                 │
│  EloquentEmployeeRepository — the only place that touches DB   │
└────────────────────────────────────────────────────────────────┘
```

**Dependency direction**: `HTTP → Application → Domain ← Infrastructure`

| Layer | Location | Responsibility |
| ----- | -------- | -------------- |
| Domain | `app/Domain/Employee/` | Repository contract + domain exceptions |
| Application | `app/Application/Employee/` | Use cases + DTOs (framework-free orchestration) |
| Infrastructure | `app/Infrastructure/Persistence/` | Eloquent implementation of the repository |
| Interface | `app/Http/Controllers/Api/`, `Requests/`, `Resources/` | HTTP ↔ application translation |

`AppServiceProvider` binds `EmployeeRepositoryInterface → EloquentEmployeeRepository`.
`bootstrap/app.php` converts `EmployeeNotFoundException` to a `404` JSON response automatically.

## Quick start

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate
# edit .env -> set DB_DATABASE / DB_USERNAME / DB_PASSWORD for your Postgres
# OCR_PROVIDER=fake is fine for local dev

# 3. Create the database (one-time)
createdb employee_app_local

# 4. Run migrations + seeders
php artisan migrate:fresh --seed

# 5. Build frontend (or use `npm run dev` for HMR)
npm run build

# 6. Serve
php artisan serve
# open http://127.0.0.1:8000
```

## Default routes

| Method | Path | Auth | Notes |
| ------ | ---- | ---- | ----- |
| GET    | `/`                      | public | landing |
| GET    | `/login`, `/register`    | public | Breeze auth |
| GET    | `/employees`             | auth   | SPA shell (Vue) |
| GET    | `/api/employees`              | auth | list (search, sort, paginate) |
| GET    | `/api/employees/{id}`         | auth | show single |
| POST   | `/api/employees`              | auth | create |
| PUT    | `/api/employees/{id}`         | auth | update |
| DELETE | `/api/employees/{id}`         | auth | delete |
| POST   | `/api/ocr/plate`              | public, `throttle:5,1` | OCR plate → JSON |
| POST   | `/api/employees/ocr/plate`    | auth, `throttle:5,1`   | same OCR, auth-gated |

## OCR provider

Switch via `.env`:

```env
# Stub provider returns a deterministic fake result. Use for local dev/tests.
OCR_PROVIDER=fake

# OpenAI GPT-4o vision:
OCR_PROVIDER=openai
OPENAI_API_KEY=sk-...
OPENAI_OCR_MODEL=gpt-4o-mini          # optional

# Google Gemini vision (free tier: ~15 req/min via AI Studio):
OCR_PROVIDER=gemini
GEMINI_API_KEY=...
GEMINI_OCR_MODEL=gemini-2.0-flash     # optional

# Groq (OpenAI-compatible, fast inference):
OCR_PROVIDER=groq
GROQ_API_KEY=gsk_...
GROQ_OCR_MODEL=meta-llama/llama-4-scout-17b-16e-instruct   # optional
```

Endpoint contract (multipart):

- Field: `image` (file, jpeg/png, max 5 MB)
- Response 200:
  ```json
  {
    "plate_text": "B 1234 CD",
    "matches_format": true,
    "confidence": 0.93,
    "raw_text": "B 1234 CD",
    "provider": "openai:gpt-4o-mini"
  }
  ```
- Response 422 when the plate cannot be determined (body still includes `raw_text`, `normalized`, `provider`).
- Response 422 also on invalid upload (wrong mime / > 5 MB).
- Response 429 when rate-limited (5 req / minute / IP).
- Response 502 when the upstream provider errors (timeout, quota, bad gateway).

## Validation

All input validation lives server-side in FormRequests (`StoreEmployeeRequest`, `UpdateEmployeeRequest`). The Vue form has no HTML5 constraint attributes; messages render only when the API returns `422` with a `errors` payload.

Key rules:

- `name`: required, string, max 30
- `nik`: required on create, string, exactly 10 chars, unique; immutable on update
- `birthdate`: required, date, at least 15 years ago
- `sex`: required, boolean
- `address`: nullable, string, max 200
- `salary`: required, numeric, >= 0
- `currency`: optional, string, exactly 3 alpha chars (e.g. `USD`); defaults to `USD`
- `is_active`: optional, boolean; defaults to `true` on create

## Tests

```bash
php artisan test
# or
vendor/bin/phpunit
```

## Useful commands

```bash
npm run dev          # Vite dev server with HMR
npm run build        # production build into public/build
php artisan migrate:fresh --seed
php artisan tinker
```

## Project layout

```
app/
  Http/Controllers/Api/   # JSON endpoints (EmployeeController, OcrController)
  Http/Requests/          # StoreEmployeeRequest, UpdateEmployeeRequest
  Http/Resources/         # EmployeeResource (shapes JSON output)
  Models/Employee.php     # ID = YYYYMM + 5-digit seq (auto-generated)
  Services/Ocr/           # PlateOcrDriver interface + Drivers/
    Drivers/              # FakePlateDriver, OpenAiPlateDriver,
                          # GeminiPlateDriver, GroqPlateDriver
resources/js/
  components/             # EmployeeApp, EmployeeForm, EmployeeDeleteConfirm,
                          # PlateOcrUpload, AppModal, FormField
  composables/            # useEmployeeList, useEmployeeDelete
  helpers/                # format.js
resources/views/          # Blade shells
routes/
  web.php                 # Web routes (SPA shell, profile, auth)
  api.php                 # API routes (employees CRUD, OCR)
database/migrations/      # Schema (employees, currency, indexes)
database/seeders/         # Seed data
```

## Flow & Process Note

One-page summary of the Employee App user + system flow.

### 1. Authentication

```
Visitor -> /login (Breeze) -> session cookie -> /employees (SPA shell)
```

Routes outside `auth` middleware: landing (`/`), Breeze auth pages, public OCR (`/api/ocr/plate`).

### 2. Employee CRUD (SPA)

```
EmployeeApp.vue (list)
    │  GET /api/employees?search=&sort=&dir=&page=
    ▼
EmployeeForm.vue (modal)
    │  POST   /api/employees           (create)
    │  PUT    /api/employees/{id}      (update, NIK immutable)
    │  DELETE /api/employees/{id}      (delete)
    ▼
EmployeeController (thin adapter)
    │ StoreEmployeeRequest / UpdateEmployeeRequest — validation
    │ builds DTO (CreateEmployeeData / UpdateEmployeeData)
    ▼
Use Case (Application layer)
    │  ListEmployeesUseCase  / GetEmployeeUseCase
    │  CreateEmployeeUseCase / UpdateEmployeeUseCase / DeleteEmployeeUseCase
    │  all accept/return DTOs or domain objects — no HTTP knowledge
    ▼
EmployeeRepositoryInterface (Domain contract)
    ▼
EloquentEmployeeRepository (Infrastructure)
    │  only Eloquent layer that touches the DB
    ▼
Employee model -> employees table (PostgreSQL)
```

Error handling: `EmployeeNotFoundException` thrown by use cases is caught by the global exception handler in `bootstrap/app.php` and rendered as `{ message }` 404 — no try/catch in the controller.

Validation strategy: the Vue form holds **no** HTML5 constraints. The server returns `422 { errors: { field: [msg] } }` and the form's `fieldError(key)` renders the first message under each input.

### 3. OCR Plate / NIK Capture

```
PlateOcrUpload.vue
    │ user picks image file
    │ POST multipart /api/ocr/plate              (public, throttle:5,1)
    │   or /api/employees/ocr/plate              (auth, throttle:5,1)
    ▼
OcrController::plate
    │ validates: image (mimes:jpg,jpeg,png; max 5120 KB)
    │ resolves OcrDriver from OCR_PROVIDER (.env)
    ▼
FakeDriver       -> returns canned { plate, raw }       (dev/tests)
OpenAiDriver     -> POSTs image to OpenAI vision model  (OCR_PROVIDER=openai)
GeminiDriver     -> POSTs image to Google Gemini vision  (OCR_PROVIDER=gemini)
GroqDriver       -> POSTs image to Groq inference API    (OCR_PROVIDER=groq)
    │
    ▼
JSON { plate, raw } -> EmployeeForm prefills NIK / name
```

OCR provider env vars:

| Provider | Required env vars | Optional (model override) |
| -------- | ----------------- | ------------------------- |
| `fake`   | —                 | —                         |
| `openai` | `OPENAI_API_KEY`  | `OPENAI_OCR_MODEL` (default: `gpt-4o-mini`) |
| `gemini` | `GEMINI_API_KEY`  | `GEMINI_OCR_MODEL` (default: `gemini-2.0-flash`) |
| `groq`   | `GROQ_API_KEY`    | `GROQ_OCR_MODEL` (default: `meta-llama/llama-4-scout-17b-16e-instruct`) |

### 4. Data model (employees)

| column      | type         | notes                                  |
| ----------- | ------------ | -------------------------------------- |
| id          | varchar(11)   | PK, YYYYMM + 5-digit seq (e.g. `20240600001`), auto-generated |
| name        | varchar(30)   | indexed                                |
| birthdate   | date          | indexed; must be >= 15y ago            |
| sex         | boolean       | true=M, false=F                        |
| address     | varchar(200)  | nullable                               |
| salary      | numeric(12,4) | indexed                                |
| currency    | varchar(3)    | default `USD`; 3-letter ISO code       |
| nik         | varchar(10)   | unique, immutable post-create          |
| is_active   | boolean       | default true                           |
| entry_date  | timestamp     | created_at alias                       |
| update_date | timestamp     | updated_at alias                       |

### 5. Error & throttle map

| Condition                       | Status | Body                              |
| ------------------------------- | ------ | --------------------------------- |
| Unauthenticated SPA API call    | 401    | `{ message }`                     |
| Validation failure              | 422    | `{ message, errors: {...} }`      |
| Missing resource                | 404    | `{ message }`                     |
| OCR throttle exceeded           | 429    | `{ message }` + `Retry-After`     |
| Server error                    | 500    | `{ message }` (debug off)         |

## 6. Local dev cheatsheet

```bash
php artisan migrate:fresh --seed
npm run dev
php artisan serve
# OCR_PROVIDER=fake by default — no external calls
```

### 7. Architecture layers (Clean / Hexagonal)

```
app/
├── Domain/Employee/
│   ├── EmployeeRepositoryInterface.php   # pure PHP contract
│   └── Exceptions/
│       └── EmployeeNotFoundException.php
├── Application/Employee/
│   ├── DTO/
│   │   ├── EmployeeListCriteria.php
│   │   ├── CreateEmployeeData.php
│   │   └── UpdateEmployeeData.php
│   ├── ListEmployeesUseCase.php
│   ├── GetEmployeeUseCase.php
│   ├── CreateEmployeeUseCase.php
│   ├── UpdateEmployeeUseCase.php
│   └── DeleteEmployeeUseCase.php
├── Infrastructure/Persistence/
│   └── EloquentEmployeeRepository.php   # implements Domain interface
└── Http/
    ├── Controllers/Api/EmployeeController.php  # thin adapter
    ├── Requests/{Store,Update}EmployeeRequest.php
    └── Resources/EmployeeResource.php
```

Binding: `AppServiceProvider::register()` — `EmployeeRepositoryInterface` → `EloquentEmployeeRepository`
Exception handler: `bootstrap/app.php` — `EmployeeNotFoundException` → 404 JSON

### 8. Test coverage snapshot

`php artisan test` covering:

- **Unit / Employee**: `ListEmployeesUseCaseTest`, `GetEmployeeUseCaseTest`, `CreateEmployeeUseCaseTest`, `DeleteEmployeeUseCaseTest` — use Mockery, BDD-style test names, zero DB I/O
- **Unit / OCR**: `PlateNormalizerTest` — for normalize, matchesFormat, extractPlate
- **Feature / Auth**: registration, login, password reset, email verification, password update
- **Feature / Employee**: auth gate, search, sort, paginate, create (with currency), show, update (NIK immutable), delete, 404/422 paths
- **Feature / Profile**: edit, update, delete account
