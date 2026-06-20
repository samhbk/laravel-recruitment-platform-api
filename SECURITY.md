# Security Policy

## Supported versions

| Version | Supported |
| ------- | --------- |
| 1.x     | Yes       |

## Reporting a vulnerability

This is a **portfolio reference API**, not a production SaaS with SLAs.

If you discover a security issue, please **do not** open a public GitHub issue with exploit details.

**Preferred:** [GitHub private vulnerability reporting](https://github.com/sameh-bakleh/laravel-recruitment-platform-api/security/advisories/new) (if enabled on the repo).

**Otherwise:** contact the maintainer privately (GitHub profile or portfolio site) with:

- Description of the issue
- Steps to reproduce
- Impact assessment (if known)

## Scope

**In scope**

- Committed secrets or credentials (`.env`, API keys, JWT private keys)
- Authentication or authorization bypass on protected endpoints
- SQL injection or mass-assignment leading to privilege escalation

**Known limitations (portfolio context)**

- Demo seed users use a shared password (`password`) — documented in README for local use only
- Docker Compose uses development database passwords — not for public deployment
- `resume_path` is a string field without file-upload validation (no resume storage pipeline in this repo)
- No WAF, DDoS protection, or certificate pinning — expected for a sample backend

## Secure usage

- Copy `.env.example` to `.env`; never commit `.env`
- Run `php artisan jwt:secret` and use a strong `JWT_SECRET` outside local demos
- Set `APP_DEBUG=false` in any shared or deployed environment
- Rotate JWT secrets if a `.env` file was ever exposed

## What we protect in this repository

- `.env` is listed in `.gitignore`
- RBAC enforced via middleware, policies, and Form Request `authorize()` hooks
- Registration limited to `job_seeker` and `company` roles (not `admin`)
- Auth endpoints rate-limited (`throttle:10,1` per minute)
- Application duplicate guard at repository + unique DB constraint
