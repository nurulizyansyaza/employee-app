# Employee App

Employee management app with OCR-driven license plate / NIK capture.

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

The test suite uses an in-memory sqlite database (see `phpunit.xml`) so Postgres isn't required to run it.

Test breakdown: **56 tests** — 19 unit (PlateNormalizer) + 37 feature (auth, profile, employee CRUD, OCR validation paths).

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
docs/flow-process-note.md # 1-page flow note
```
