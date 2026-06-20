# Contributing

Thank you for your interest in this project. It is a **portfolio reference API** — contributions that improve clarity, tests, security, or maintainability are welcome.

## Before you start

1. Read the [README](README.md) for scope and architecture.
2. API and RBAC details: [docs/api.md](docs/api.md), [docs/auth-and-rbac.md](docs/auth-and-rbac.md).
3. Check existing [issues](https://github.com/sameh-bakleh/laravel-recruitment-platform-api/issues) to avoid duplicate work.
3. For security issues, follow [SECURITY.md](SECURITY.md) — **do not** open public issues with exploit details.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

## Development workflow

1. Fork the repository and create a branch from `main`.
2. Make focused changes — one concern per pull request.
3. Run checks locally before opening a PR:

```bash
composer lint    # Laravel Pint (dry run)
composer test    # PHPUnit
```

4. Open a pull request using the [PR template](.github/pull_request_template.md).

## Code guidelines

- Match existing structure: thin controllers, Form Requests, Actions, repository interfaces.
- Use `declare(strict_types=1);` in new PHP files under `app/`.
- Add or update **feature tests** when changing HTTP behavior or permissions.
- Do not commit `.env`, `vendor/`, IDE folders, or generated cache/logs.
- Keep demo/seed data synthetic (`@example.com` only).

## What we are likely to accept

- Bug fixes with tests
- Test coverage for untested endpoints
- Documentation and OpenAPI accuracy
- CI/CD improvements that stay simple
- Small refactors that reduce duplication without over-abstraction

## What we may decline

- Large rewrites or framework migrations
- Production deployment configs with real credentials
- Features that require external paid services without clear portfolio value
- Changes that remove RBAC checks or weaken validation

## Questions

Open a [discussion](https://github.com/sameh-bakleh/laravel-recruitment-platform-api/discussions) or issue labeled `question` for clarification before large changes.
