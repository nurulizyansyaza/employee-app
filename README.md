# Employee App

A Laravel + Vue SPA for managing employees. The interesting bit: you can snap a photo of a license plate or ID card and it'll try to read the NIK for you using your choice of AI vision model (OpenAI, Gemini, Groq or a local fake for dev).

## Getting started

```bash
composer install && npm install

cp .env.example .env
php artisan key:generate
# Set DB_DATABASE / DB_USERNAME / DB_PASSWORD to point at your Postgres instance
# OCR_PROVIDER=fake works out of the box — no API keys needed locally

createdb employee_app_local   # one-time
php artisan migrate:fresh --seed

npm run dev       # Vite + HMR
php artisan serve # http://127.0.0.1:8000
```

## OCR

Upload a JPEG or PNG (max 5 MB) and the app returns the plate/NIK text. Swap providers in `.env`:

```env
OCR_PROVIDER=fake    # default — returns a canned response, no external calls

OCR_PROVIDER=openai
OPENAI_API_KEY=sk-...
OPENAI_OCR_MODEL=gpt-4o   # optional

OCR_PROVIDER=gemini
GEMINI_API_KEY=...
GEMINI_OCR_MODEL=gemini-2.0-flash   # optional

OCR_PROVIDER=groq
GROQ_API_KEY=gsk_...
GROQ_OCR_MODEL=meta-llama/llama-4-scout-17b-16e-instruct   # optional
```

Successful response:

```json
{
  "plate_text": "B 1234 CD",
  "matches_format": true,
  "confidence": 0.93,
  "raw_text": "B 1234 CD",
  "provider": "openai:gpt-4o-mini"
}
```

Returns `422` if the plate can't be read or the upload is invalid, `429` if you hit the rate limit (5 req/min/IP) and `502` if the upstream provider is down.

## Routes

| Method | Path | Auth |
| ------ | ---- | ---- |
| GET | `/` | public |
| GET | `/login`, `/register` | public |
| GET | `/employees` | required |
| GET/POST | `/api/employees` | required |
| GET/PUT/DELETE | `/api/employees/{id}` | required |
| POST | `/api/ocr/plate` | public (rate-limited) |
| POST | `/api/employees/ocr/plate` | required (rate-limited) |

## Validation

Validation is entirely server-side (`StoreEmployeeRequest`, `UpdateEmployeeRequest`). The Vue form has no HTML5 constraints. Errors only appear when the API sends back a `422`.

| Field | Rules |
| ----- | ----- |
| `name` | required, max 30 chars |
| `nik` | required on create, exactly 10 chars, unique, it can't be changed after creation |
| `birthdate` | required, must be at least 15 years ago |
| `sex` | required, boolean |
| `address` | optional, max 200 chars |
| `salary` | required, numeric, >= 0 |
| `currency` | optional, 3-letter ISO code (e.g. `USD`), defaults to `USD` |
| `is_active` | optional, boolean, defaults to `true` |

## Architecture

The app is structured around Clean / Hexagonal Architecture. The idea is that the core logic has no idea Laravel even exists.

```
HTTP layer       app/Http/                    thin controllers, FormRequests, Resources
Application      app/Application/Employee/    use cases + DTOs — no framework imports
Domain           app/Domain/Employee/         repository interface + exceptions (pure PHP)
Infrastructure   app/Infrastructure/          EloquentEmployeeRepository — only DB code
```

`AppServiceProvider` wires up `EmployeeRepositoryInterface → EloquentEmployeeRepository`. `EmployeeNotFoundException` is caught globally in `bootstrap/app.php` and turned into a 404 — no try/catch needed in controllers.

## Data model

Employee IDs are auto-generated as `YYYYMM` + a 5-digit sequence (e.g. `20240600001`).

| Column | Type | Notes |
| ------ | ---- | ----- |
| `id` | varchar(11) | auto-generated PK |
| `name` | varchar(30) | |
| `nik` | varchar(10) | unique, immutable |
| `birthdate` | date | |
| `sex` | boolean | true = M |
| `address` | varchar(200) | nullable |
| `salary` | numeric(12,4) | |
| `currency` | varchar(3) | default `USD` |
| `is_active` | boolean | default true |
| `entry_date` | timestamp | created_at |
| `update_date` | timestamp | updated_at |

## Tests

```bash
php artisan test
```

Covers unit tests for all use cases (Mockery, zero DB I/O), the plate normalizer, feature tests for auth, full employee CRUD and profile management.

## Handy commands

```bash
npm run build                    # production asset build
php artisan migrate:fresh --seed # reset + reseed the DB
php artisan tinker               # REPL
```

## How things flow

### 1. Auth

Breeze handles this. Visitors hit `/login` or `/register`, get a session cookie and land on `/employees`. That's it. The only public API route is `/api/ocr/plate`, everything else requires a session.

### 2. Employee CRUD

`EmployeeApp.vue` drives the list view, search, sort and paginate all hit `GET /api/employees`. Opening the form modal calls `POST` or `PUT` depending on whether you're creating or editing. Delete goes through a confirm modal first.

On the server side, the controller stays thin on purpose. It takes the request, builds a DTO, hands it to a use case and returns the result. The use case talks to `EmployeeRepositoryInterface` which `EloquentEmployeeRepository` implements. The use cases themselves don't know anything about HTTP or Eloquent, that's the whole point.

When an employee isn't found, `EmployeeNotFoundException` bubbles up and the global handler in `bootstrap/app.php` turns it into a 404. No try/catch in the controller.

Validation errors come back as `422 { errors: { field: [messages] } }`. The Vue form has no HTML5 constraints, it just renders whatever the server sends back.

### 3. OCR

`PlateOcrUpload.vue` lets you pick an image. It POSTs to `/api/ocr/plate` (public) or `/api/employees/ocr/plate` (auth). The controller validates the upload, resolves the right driver from `OCR_PROVIDER`, calls it and returns the result. If it works, the form auto-fills the NIK and name fields.

The `fake` driver just returns a hardcoded response, it is useful for local dev and tests where you don't want real API calls. Swap to `openai`, `gemini` or `groq` when you actually need it to read something.

## Database and Test Data

Sample database dumps and test data files are available here:
[Google Drive – Database & Test Data](https://drive.google.com/drive/folders/1SFV8XwQhLJy8NcfAOmgzKE1ikUO-baKn?usp=sharing)
