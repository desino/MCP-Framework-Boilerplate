# desino/mcp-framework-boilerplate

Laravel scaffolding package that publishes the Desino MCP reference application into your host app via a single Artisan command — similar to [desino/boilerplate](https://github.com/desino/LaravelBoilerPlate).

After installation, MCP code lives in your application (`app/`, `routes/`, `resources/`, etc.) and can be edited like first-party code.

## What gets published

- MCP server at `/mcp` (`routes/ai.php`) with bearer token authentication
- Admin UI to manage MCP tools (`/mcp_tools`)
- Built-in library tools (GitHub, PM tool, project resolver)
- Custom MCP tool generator and stubs
- Models, migrations, services, views, and translations

## Requirements

- PHP ^8.3
- Laravel ^13
- [laravel/mcp](https://github.com/laravel/mcp)
- [desino/boilerplate](https://github.com/desino/LaravelBoilerPlate)

## Installation

### 1. Require the package

```bash
composer require desino/mcp-framework-boilerplate
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
        "desino/mcp-framework-boilerplate": "@dev"
    }
}
```

### 2. Publish Desino boilerplate (if not already done)

```bash
php artisan make:desino-boilerplate
composer update
```

### 3. Publish MCP boilerplate

```bash
php artisan make:desino-mcp-framework-boilerplate
```

Use `--force` to overwrite files that already exist.

This copies MCP scaffolding into your application:

| Published to | Contents |
|---|---|
| `app/Http/Controllers/McpToolController.php` | Admin UI controller |
| `app/Http/Middleware/VerifyMcpToken.php` | MCP bearer token middleware |
| `app/Mcp/Servers/BoilerplateServer.php` | Dynamic MCP server |
| `app/Mcp/Tools/` | Library MCP tools |
| `app/Models/` | `McpTool`, `McpProject`, `McpAuditLog` |
| `app/Services/` | Tool registry, GitHub/PM services, generator |
| `config/mcp_support.php` | MCP configuration |
| `routes/ai.php` | MCP HTTP endpoint registration |
| `resources/views/mcp_tools/` | Admin Blade views |
| `database/migrations/` | MCP database tables |
| `stubs/mcp/` | Custom tool generation stubs |

The command also merges:

- MCP tool routes into `routes/web.php` (inside the `checkIfAdmin` middleware group)
- MCP translation keys into `lang/en/messages.php`
- `verifyMcpToken` middleware alias into `bootstrap/app.php`

### 4. Configure environment

```env
MCP_API_TOKEN=your-secret-token

MCP_GITHUB_API_URL=https://api.github.com
MCP_GITHUB_TIMEOUT=20

PM_TOOL_API_URL=https://pmtool.example.com
PM_TOOL_API_KEY=your-pm-tool-key
PM_TOOL_API_TIMEOUT=20
PM_TOOL_API_VERIFY=true
```

### 5. Run migrations

```bash
php artisan migrate
```

Tables created:

- `mcp_projects`
- `mcp_tools`
- `mcp_audit_logs`

### 6. Add navigation link (optional)

Add an MCP Tools menu item to your layout:

```blade
<a class="nav-link" href="{{ route('mcpTools.index') }}">
    {{ __('messages.main_menu_mcp_tools_title') }}
</a>
```

## Usage

### MCP endpoint

Active tools registered in the admin UI are exposed at:

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

### Library tools

- `support.resolve_project` — Resolve project details from a project code
- `pmtool.search_tickets` — Smart PM tool ticket search
- `github.search_code` — GitHub code search
- `github.read_file` — Read a file from GitHub
- `github.recent_commits` — List recent commits

### Custom tools

Custom tools are generated into `app/Mcp/Tools/` when created from the admin UI.

## Configuration

Key options in `config/mcp_support.php`:

| Key | Description |
|-----|-------------|
| `mcp_api_token` | Bearer token for MCP requests |
| `github_api_url` | GitHub API base URL |
| `github_timeout` | GitHub API timeout (seconds) |
| `pmtool.base_url` | PM tool API URL |
| `pmtool.api_key` | PM tool API key |

## Package vs application code

This package is a **thin installer**. It only ships:

- `MakeMcpBoilerplateCommand` (`php artisan make:desino-mcp-framework-boilerplate`)
- Application stubs under `src/stubs/`

All runtime MCP logic runs from your host application's `app/` directory after publishing.

## License

MIT — see [LICENSE](LICENSE).
