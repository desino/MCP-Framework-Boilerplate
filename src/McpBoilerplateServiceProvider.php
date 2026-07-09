<?php

namespace Desino\McpBoilerplate;

use Desino\McpBoilerplate\Http\Middleware\VerifyMcpToken;
use Desino\McpBoilerplate\Mcp\Servers\BoilerplateServer;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Mcp\Facades\Mcp;

class McpBoilerplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mcp-boilerplate.php', 'mcp-boilerplate');

        // Backward-compatible config alias used by existing MCP tooling.
        $this->app->booted(function (): void {
            config([
                'mcp_support' => config('mcp-boilerplate'),
            ]);
        });
    }

    public function boot(): void
    {
        $this->registerMcpRoutes();
        $this->registerWebRoutes();
        $this->registerMiddleware();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mcp-boilerplate');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mcp-boilerplate');

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    protected function registerMcpRoutes(): void
    {
        $path = config('mcp-boilerplate.mcp_path', '/mcp');

        Mcp::web($path, BoilerplateServer::class)
            ->middleware(VerifyMcpToken::class);
    }

    protected function registerWebRoutes(): void
    {
        Route::group([
            'prefix' => config('mcp-boilerplate.route_prefix', ''),
            'middleware' => config('mcp-boilerplate.admin_middleware', ['auth', 'checkIfAdmin']),
            'as' => config('mcp-boilerplate.route_name_prefix', 'mcpTools.'),
        ], __DIR__.'/../routes/web.php');
    }

    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('verifyMcpToken', VerifyMcpToken::class);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/mcp-boilerplate.php' => config_path('mcp-boilerplate.php'),
        ], 'mcp-boilerplate-config');

        $this->publishes([
            __DIR__.'/../resources/views/mcp_tools' => resource_path('views/vendor/mcp-boilerplate/mcp_tools'),
        ], 'mcp-boilerplate-views');

        $this->publishes([
            __DIR__.'/../resources/lang/en/messages.php' => lang_path('en/mcp-boilerplate.php'),
        ], 'mcp-boilerplate-lang');

        $this->publishes([
            __DIR__.'/../stubs/mcp' => base_path('stubs/mcp'),
        ], 'mcp-boilerplate-stubs');
    }
}
