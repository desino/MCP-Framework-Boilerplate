# desino/mcp-boilerplate

Installable Laravel package that provides the MCP boilerplate from the Desino MCP reference application:

- Dynamic MCP server at `/mcp` with bearer token authentication
- Admin UI to manage MCP tools (library + custom generated tools)
- Built-in support tools: project resolver, GitHub code search/read/commits, PM tool ticket search
- Audit logging for MCP tool executions
- Database-backed project configuration for multi-project support

## Requirements

- PHP ^8.3
- Laravel ^13
- [laravel/mcp](https://github.com/laravel/mcp) ^0.8
- Recommended: [desino/boilerplate](https://github.com/desino/LaravelBoilerPlate) for admin auth, layout, and redirect helpers

## Installation

### 1. Require the package

```bash
composer require desino/mcp-boilerplate
```

For local development from this repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../mcpFrameworkBoilerplate"
        }
    ],
    "require": {
        "desino/mcp-boilerplate": "@dev"
    }
}
```

### 2. Publish configuration (optional)

```bash
php artisan vendor:publish --tag=mcp-boilerplate-config
```

### 3. Configure environment

```env
MCP_API_TOKEN=your-secret-token

MCP_GITHUB_API_URL=https://api.github.com
MCP_GITHUB_TIMEOUT=20

PM_TOOL_API_URL=https://pmtool.example.com
PM_TOOL_API_KEY=your-pm-tool-key
PM_TOOL_API_TIMEOUT=20
PM_TOOL_API_VERIFY=true
```

### 4. Run migrations

```bash
php artisan migrate
```

The package auto-loads its migrations for:

- `mcp_projects`
- `mcp_tools`
- `mcp_audit_logs`

### 5. Register admin middleware (if using desino/boilerplate)

Ensure your host app registers the admin middleware alias used by the MCP tool manager routes:

```php
// bootstrap/app.php
$middleware->alias([
    'checkIfAdmin' => \App\Http\Middleware\CheckUserIsAdmin::class,
]);
```

### 6. Add navigation link (optional)

Add an MCP Tools menu item to your layout, for example in `resources/views/layouts/app.blade.php`:

```blade
<a class="nav-link" href="{{ route('mcpTools.index') }}">
    {{ __('mcp-boilerplate::messages.main_menu_mcp_tools_title') }}
</a>
```

Or merge the translation into your app language file:

```bash
php artisan vendor:publish --tag=mcp-boilerplate-lang
```

## Usage

### MCP endpoint

Once installed, active tools registered in the admin UI are exposed at:

```
POST /mcp
Authorization: Bearer {MCP_API_TOKEN}
```

### Admin UI routes

| Route | Name |
|-------|------|
| `/mcp_tools` | `mcpTools.index` |
| `/mcp_tools/create` | `mcpTools.create` |
| `/mcp_tools/{id}/edit` | `mcpTools.edit` |

These routes use the middleware configured in `config/mcp-boilerplate.php` (default: `auth`, `checkIfAdmin`).

### Library tools

The following tools ship with the package and can be registered from the admin UI:

- `support.resolve_project` — Resolve project details from a project code
- `pmtool.search_tickets` — Smart PM tool ticket search
- `github.search_code` — GitHub code search
- `github.read_file` — Read a file from GitHub
- `github.recent_commits` — List recent commits

### Custom tools

Custom tools are generated into `app/Mcp/Tools/` in the host application. Configure the target namespace/path in `config/mcp-boilerplate.php` if needed.

## Configuration

Key options in `config/mcp-boilerplate.php`:

| Key | Description |
|-----|-------------|
| `mcp_path` | MCP HTTP endpoint path (default: `/mcp`) |
| `mcp_api_token` | Bearer token for MCP requests |
| `admin_middleware` | Middleware for admin UI routes |
| `redirect_helper` | Host class providing redirect flash messages (default: `App\Services\AppMiscService`) |
| `layout_view` | Blade layout for admin views (default: `layouts.app`) |
| `custom_tools.namespace` | Namespace for generated custom tools |
| `custom_tools.path` | Directory for generated custom tool classes |

## Publishing assets

```bash
# Config
php artisan vendor:publish --tag=mcp-boilerplate-config

# Views (override package views)
php artisan vendor:publish --tag=mcp-boilerplate-views

# Language lines
php artisan vendor:publish --tag=mcp-boilerplate-lang

# Custom tool stubs
php artisan vendor:publish --tag=mcp-boilerplate-stubs
```

## License

MIT — see [LICENSE](LICENSE).
