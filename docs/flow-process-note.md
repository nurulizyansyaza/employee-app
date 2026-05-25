# Flow & Process Note

One-page summary of the Employee App user + system flow.

## 1. Authentication

```
Visitor -> /login (Breeze) -> session cookie -> /employees (SPA shell)
```

Routes outside `auth` middleware: landing (`/`), Breeze auth pages, public OCR (`/api/ocr/plate`).

## 2. Employee CRUD (SPA)

```
EmployeeApp.vue (list)
    │  GET /api/employees?search=&sort=&dir=&page=
    ▼
EmployeeForm.vue (modal)
    │  POST   /api/employees           (create)
    │  PUT    /api/employees/{id}      (update, NIK immutable)
    │  DELETE /api/employees/{id}      (delete)
    ▼
EmployeeController -> StoreEmployeeRequest / UpdateEmployeeRequest
    │ validation (server-side only)
    ▼
Employee model -> employees table (Postgres)
```

Validation strategy: the Vue form holds **no** HTML5 constraints. The server returns `422 { errors: { field: [msg] } }` and the form's `fieldError(key)` renders the first message under each input.

## 3. OCR Plate / NIK Capture

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

## 4. Data model (employees)

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

## 5. Error & throttle map

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

## 7. Test coverage snapshot

`php artisan test` runs **56 tests** (19 unit + 37 feature) covering:

- **Unit**: `PlateNormalizerTest` — 18 cases for normalize, matchesFormat, extractPlate
- **Feature / Auth**: registration, login, password reset, email verification, password update (18 tests)
- **Feature / Employee**: auth gate, search, sort, paginate, create (with currency), update (NIK immutable), delete, 404/422 paths (13 tests)
- **Feature / Profile**: edit, update, delete account (5 tests)
- **Feature / Other**: basic HTTP smoke test (1 test)
