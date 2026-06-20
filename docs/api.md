# API reference

Base URL (local): `http://localhost:8000/api/v1`

Health check: `GET /up` (no version prefix)

All JSON responses use `application/json`. Successful single-resource responses are wrapped in a `data` key via Laravel API Resources. Paginated collections include `data` and `meta` (and `links` where applicable).

Machine-readable contract: [`openapi.yaml`](../openapi.yaml) — import into Swagger UI, Stoplight, or Bruno.

---

## Authentication

Protected routes require:

```
Authorization: Bearer <access_token>
```

Obtain a token via `POST /auth/register` or `POST /auth/login`. Refresh with `POST /auth/refresh` before expiry (`JWT_TTL` minutes, default 60).

| Endpoint | Auth | Notes |
|----------|------|-------|
| `POST /auth/register` | Public | Rate-limited: 10 req/min |
| `POST /auth/login` | Public | Rate-limited: 10 req/min |
| `POST /auth/logout` | JWT | Invalidates current token |
| `POST /auth/refresh` | JWT | Returns new `access_token` |
| `GET /auth/me` | JWT | Current user profile |

**Token response shape (`201` register / `200` login / refresh):**

```json
{
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Demo Candidate",
      "email": "candidate@example.com",
      "role": "job_seeker"
    }
  }
}
```

**Register body:**

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "job_seeker"
}
```

`role` is optional (`job_seeker` default). Only `job_seeker` and `company` are allowed at registration — not `admin`.

---

## Job listings

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/job-listings` | Optional JWT | Published catalog (cached) |
| GET | `/job-listings/{id}` | Optional JWT | Single published job |
| POST | `/job-listings` | JWT + policy | Create (company/admin) |
| PUT | `/job-listings/{id}` | JWT + policy | Update (owner/admin) |
| DELETE | `/job-listings/{id}` | JWT + policy | Soft delete (owner/admin) |
| GET | `/my/job-listings` | JWT | Employer's own listings |

**Catalog query parameters (`GET /job-listings`):**

| Param | Type | Description |
|-------|------|-------------|
| `employment_type` | string | `full_time`, `part_time`, `contract`, `remote` |
| `location` | string | Substring match |
| `search` | string | Title or company name |
| `salary_min` | number | Minimum salary filter |
| `salary_max` | number | Maximum salary filter |
| `page` | integer | Pagination |

When authenticated as a job seeker, each listing may include `is_saved: true|false`.

**Create body (required fields):**

```json
{
  "title": "Backend Engineer",
  "description": "Build APIs with Laravel.",
  "company_name": "Demo GmbH",
  "location": "Berlin, DE",
  "employment_type": "full_time",
  "salary_min": 70000,
  "salary_max": 95000,
  "salary_currency": "EUR",
  "is_published": true,
  "skills": ["PHP", "Laravel", "MySQL"]
}
```

---

## Applications (ATS workflow)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/applications` | JWT | Alias for candidate's applications |
| GET | `/my/applications` | JWT | Candidate's applications |
| POST | `/applications` | JWT | Apply to a published job |
| GET | `/applications/{id}` | JWT | Applicant or listing owner |
| PATCH | `/applications/{id}/status` | JWT | Employer updates pipeline status |
| GET | `/job-listings/{id}/applications` | JWT | Applications for a job (owner) |

**Apply body:**

```json
{
  "job_listing_id": 1,
  "cover_letter": "I am interested in this role.",
  "resume_path": "/uploads/demo-resume.pdf"
}
```

`resume_path` is a string reference only — no file upload pipeline in this repo.

**Status update body (employer):**

```json
{
  "status": "shortlisted"
}
```

Allowed values: `pending`, `reviewed`, `shortlisted`, `rejected`, `hired`.

Duplicate applications return `422` (unique constraint on `job_listing_id` + `user_id`).

---

## Company profiles

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/companies/{slug}` | Public | Public company page |
| POST | `/companies` | JWT `company` | Create profile (one per user) |
| GET | `/companies/me/profile` | JWT `company` | Own profile |
| PUT | `/companies/me/profile` | JWT `company` | Update own profile |

Route model binding resolves `{slug}` via the `Company` model (`getRouteKeyName()` → `slug`).

---

## Job seeker profiles & saved jobs

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/me/profile/job-seeker` | JWT seeker/admin | View profile |
| PUT | `/me/profile/job-seeker` | JWT seeker/admin | Upsert skills & preferences |
| GET | `/me/saved-jobs` | JWT seeker/admin | Saved job listings |
| POST | `/job-listings/{id}/save` | JWT seeker/admin | Bookmark a job |
| DELETE | `/job-listings/{id}/save` | JWT seeker/admin | Remove bookmark |
| GET | `/me/recommendations` | JWT seeker/admin | Skill-based job suggestions |

**Job seeker profile body:**

```json
{
  "headline": "Senior PHP Engineer",
  "bio": "APIs, Laravel, relational data.",
  "skills": ["PHP", "Laravel", "Redis"],
  "preferred_locations": ["Remote", "Berlin"],
  "preferred_employment_types": ["full_time", "remote"]
}
```

---

## Salary analytics

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/salary-analytics` | Public | Aggregated salary stats (cached) |

Query: `employment_type`, `location` (optional filters).

---

## Notifications

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/notifications` | JWT | Paginated in-app notifications |
| PATCH | `/notifications/{id}/read` | JWT | Mark one read |
| POST | `/notifications/read-all` | JWT | Mark all read |

Notifications are queued (`ShouldQueue`) on application events. Run `php artisan queue:work` locally, or set `QUEUE_CONNECTION=sync` for demos.

---

## Admin

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/admin/users` | JWT `admin` | Paginated user list |

---

## Common HTTP status codes

| Code | Meaning |
|------|---------|
| `200` | Success |
| `201` | Created |
| `204` | No content (delete, mark read) |
| `401` | Missing or invalid JWT |
| `403` | Authenticated but forbidden (RBAC / policy) |
| `404` | Resource not found |
| `422` | Validation error or domain rule violation |
| `429` | Rate limit exceeded (auth endpoints) |

**Validation error shape:**

```json
{
  "message": "The email field must be a valid email address.",
  "errors": {
    "email": ["The email field must be a valid email address."]
  }
}
```

---

## Quick smoke test

```bash
# Login as seeded candidate
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"candidate@example.com","password":"password"}' \
  | jq -r '.data.access_token')

# List jobs
curl -s http://127.0.0.1:8000/api/v1/job-listings \
  -H "Authorization: Bearer $TOKEN" | jq '.meta'

# Recommendations
curl -s http://127.0.0.1:8000/api/v1/me/recommendations \
  -H "Authorization: Bearer $TOKEN" | jq '.data | length'
```

See [auth-and-rbac.md](auth-and-rbac.md) for roles, middleware, and policy details.
