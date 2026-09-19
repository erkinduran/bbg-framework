# BBG Framework

A minimal PHP micro-framework written from scratch — array-based routing, a thin
PDO wrapper, plain-PHP views and a handful of global helpers. No Symfony
components, no service container, no magic. Roughly 300 lines of application
code in total.

This is a learning/reference project: the goal was to understand what a
framework actually does by building the smallest thing that still handles
routing, configuration, database access and templating.

## Features

| Component | What it does |
|---|---|
| **Router** (`src/App.php`) | Maps a URI to a `[Class, method]` pair from a flat config array |
| **DB** (`src/Core/DB.php`) | Lazily-opened PDO singleton with `prepare` / `select` / `insert` / `close` |
| **Views** (`helpers/view.php`) | Renders a plain-PHP template from `views/`, returns it as a string |
| **Config** | `.env` driven via `vlucas/phpdotenv`, read through an `env()` helper |
| **Responses** | Return a string to emit HTML; return an array or object and `index.php` sends JSON |

## Requirements

- PHP 8.0 or newer
- ext-pdo, ext-json, ext-mbstring
- Composer
- MySQL (only if you use the `DB` class)

## Installation

```bash
git clone https://github.com/erkinduran/bbg-framework.git
cd bbg-framework
composer install
cp .env.example .env
```

Then fill in `.env`:

```ini
APP_URL=bbg.test
SSL_SECURE=FALSE

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

`APP_URL` is the bare host — the `url()` helper prepends `http://` or `https://`
depending on `SSL_SECURE`.

## Running it

Every request has to reach `index.php`; the framework has no front-controller
rewrite rules of its own, so the web server needs to provide them.

**PHP built-in server (development).** Create `server.php` in the project root:

```php
<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;          // let the built-in server serve real files
}
require __DIR__ . '/index.php';
```

```bash
php -S 127.0.0.1:8000 server.php
```

Pointing `php -S` straight at `index.php` also works for routes, but then
requests for `public/css/app.css` and friends are swallowed by the router.

**Apache.** Drop an `.htaccess` in the project root:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

**nginx.**

```nginx
location / {
    try_files $uri $uri/ /index.php;
}
```

## Usage

### 1. Register a route

`config/route.php` is a flat map of URI → `[controller, method]`:

```php
<?php
    return [
        "/"        => [\App\Front\Home::class, "index"],
        "/about"   => [\App\Front\Home::class, "about"],
        "/api/ping"=> [\App\Api\Ping::class,   "index"],
    ];
```

Matching is exact-string against `$_SERVER['REQUEST_URI']` — see
[Known limitations](#known-limitations).

### 2. Write a controller

Controllers are plain classes under `src/`, autoloaded as `App\` (PSR-4).

```php
<?php

namespace App\Front;

class Home
{
    public function index()
    {
        return view("welcome", ["name" => "world"]);   // HTML
    }

    public function about()
    {
        return ["status" => true, "page" => "about"];  // JSON
    }
}
```

Whatever a controller returns decides the response type: a **string** is echoed
as-is, an **array or object** is JSON-encoded with a
`Content-type: application/json` header.

### 3. Add a view

Views are plain PHP files in `views/`. The second argument of `view()` is
extracted into local variables:

```php
<!-- views/welcome.php -->
<h1>Hello <?= htmlspecialchars($name) ?></h1>
<link rel="stylesheet" href="<?= url('public/css/app.css') ?>">
```

### 4. Query the database

`DB` opens one PDO connection on boot and closes it after the response.

```php
use App\Core\DB;

$stmt = DB::prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);
```

Use `DB::prepare()` with bound parameters for anything involving user input.
`DB::select()` and `DB::insert()` pass a raw string to `PDO::query()` and do no
escaping whatsoever.

## Helpers

Everything in `helpers/` is `require`d at boot, so these are globally available.

| Helper | Signature | Purpose |
|---|---|---|
| `env()` | `env(string $key): ?string` | Reads a value from `$_ENV`, `NULL` if unset |
| `view()` | `view(string $name, array $data = []): string` | Renders `views/{name}.php`, throws if missing |
| `url()` | `url(string $path = "/"): string` | Builds an absolute URL from `APP_URL` + `SSL_SECURE` |
| `r()` | `r($query, $message, $failedMessage, $dmessage = null): array` | Shapes a `["status" => bool, "message" => …]` JSON payload |

## Project structure

```
├── config/
│   └── route.php          # URI → [controller, method]
├── helpers/               # globally required functions
│   ├── env.php
│   ├── result.php
│   ├── url.php
│   ├── utilities.php
│   └── view.php
├── public/                # static assets
│   ├── css/
│   └── js/
├── src/                   # PSR-4 autoloaded as App\
│   ├── App.php            # the router
│   ├── Core/DB.php        # PDO wrapper
│   └── Front/Home.php     # example controller
├── views/                 # plain-PHP templates
│   └── welcome.php
├── .env.example
├── composer.json
└── index.php              # front controller / bootstrap
```

## Known limitations

This is deliberately a small project, and it shows. Before using it for
anything real:

- **No 404 handling.** An unregistered URI hits an undefined array key and
  raises an `Error`, which `App::run()` does not catch (it only catches
  `\Exception`) — so you get an uncaught fatal error, and with `display_errors`
  on, a stack trace containing filesystem paths.
- **Exact-string route matching.** `$_SERVER['REQUEST_URI']` includes the query
  string, so `/about?ref=x` does not match a `/about` route. There are no route
  parameters, and no HTTP-method dispatch — `GET /x` and `POST /x` hit the same
  handler.
- **Connection failures are silent.** `DB::__construct()` swallows the
  `PDOException`; the failure only surfaces later as a generic
  `{"outcome":false,"message":"Unable to connect"}` from `DB::check()`, which
  `die()`s mid-request.
- **`DB::select()` / `DB::insert()` take raw SQL.** No binding, no escaping.
- **No CSRF protection, no input validation, no session layer, no output
  escaping in views** — escape with `htmlspecialchars()` yourself.
- **The project root is the document root.** `index.php`, `config/`, `src/` and
  `.env` all sit beside the public assets, so the web server must be configured
  not to serve them. A conventional layout would put a `public/index.php` at the
  document root instead.
- **No tests ship with the framework.** PHPUnit is wired in as a dev dependency;
  add cases under `tests/` and run `vendor/bin/phpunit tests`.

## License

MIT — see [LICENSE](LICENSE).
