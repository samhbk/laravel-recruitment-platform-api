<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recruitment Platform API</title>
    <style>
        :root { color-scheme: light dark; font-family: system-ui, sans-serif; line-height: 1.5; }
        body { max-width: 42rem; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        p.lead { color: #666; margin-top: 0; }
        ul { padding-left: 1.25rem; }
        a { color: #2563eb; }
        code { font-size: 0.9em; }
        .badge { display: inline-block; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
    <p class="badge">Portfolio sample · synthetic data only</p>
    <h1>Recruitment Platform API</h1>
    <p class="lead">ATS-style job portal — listings, applications, employer workflows, RBAC, and salary analytics.</p>

    <h2>Documentation</h2>
    <ul>
        <li><a href="https://github.com/sameh-bakleh/laravel-recruitment-platform-api/blob/main/docs/api.md">API guide</a></li>
        <li><a href="https://github.com/sameh-bakleh/laravel-recruitment-platform-api/blob/main/openapi.yaml">OpenAPI 3 contract</a></li>
        <li><a href="https://github.com/sameh-bakleh/laravel-recruitment-platform-api/blob/main/docs/auth-and-rbac.md">Auth &amp; RBAC</a></li>
        <li><a href="/up">Health check</a> — <code>/up</code></li>
    </ul>

    <h2>Demo logins (after seed)</h2>
    <p>Password for all: <code>password</code></p>
    <ul>
        <li><code>admin@example.com</code> — admin</li>
        <li><code>employer@example.com</code> — company</li>
        <li><code>candidate@example.com</code> — job seeker</li>
    </ul>

    <h2>Sample endpoints</h2>
    <ul>
        <li><code>POST /api/auth/login</code> — JWT access token</li>
        <li><code>GET /api/jobs</code> — public job listings</li>
        <li><code>POST /api/applications</code> — submit application (candidate)</li>
    </ul>
</body>
</html>
