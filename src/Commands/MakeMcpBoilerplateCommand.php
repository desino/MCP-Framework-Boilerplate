<?php

namespace Desino\McpBoilerplate\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class MakeMcpBoilerplateCommand extends Command
{
    protected $signature = 'make:desino-mcp-boilerplate {--force : Overwrite files that already exist}';

    protected $description = 'Publish Desino MCP boilerplate files into your Laravel application.';

    protected int $published = 0;

    protected int $skipped = 0;

    public function handle(): int
    {
        $this->publishConfig();
        $this->publishMiddleware();
        $this->publishModels();
        $this->publishData();
        $this->publishServices();
        $this->publishMcpTools();
        $this->publishMcpServer();
        $this->publishControllers();
        $this->publishMigrations();
        $this->publishViews();
        $this->publishCustomToolStubs();
        $this->publishAiRoutes();
        $this->publishWebRoutes();
        $this->publishTranslations();
        $this->publishBootstrapMiddleware();

        $this->newLine();
        $this->info("Desino MCP boilerplate published ({$this->published} files".($this->skipped ? ", {$this->skipped} skipped" : '').').');
        $this->newLine();
        $this->comment('Next steps:');
        $this->comment('  php artisan migrate');
        $this->comment('Add an MCP Tools nav link in resources/views/layouts/app.blade.php if needed.');
        $this->comment('See vendor/desino/mcp-boilerplate README.md for details.');

        return self::SUCCESS;
    }

    protected function stubPath(string $path): string
    {
        return __DIR__.'/../stubs/'.$path;
    }

    protected function putFile(string $path, string $contents): void
    {
        if (file_exists($path) && ! $this->option('force')) {
            $this->warn('Skipped (exists): '.$path);
            $this->skipped++;

            return;
        }

        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $contents);
        $this->published++;
    }

    protected function putStub(string $path, string $stubPath): void
    {
        $this->putFile($path, file_get_contents($stubPath));
    }

    protected function publishConfig(): void
    {
        $this->putStub(config_path('mcp_support.php'), $this->stubPath('config/mcp_support.stub'));
    }

    protected function publishMiddleware(): void
    {
        $this->putStub(
            app_path('Http/Middleware/VerifyMcpToken.php'),
            $this->stubPath('app/Http/Middleware/VerifyMcpToken.stub')
        );
    }

    protected function publishModels(): void
    {
        foreach (['McpAuditLog', 'McpProject', 'McpTool'] as $model) {
            $this->putStub(
                app_path("Models/{$model}.php"),
                $this->stubPath("app/Models/{$model}.stub")
            );
        }
    }

    protected function publishData(): void
    {
        $this->putStub(
            app_path('Data/CustomMcpToolContext.php'),
            $this->stubPath('app/Data/CustomMcpToolContext.stub')
        );
    }

    protected function publishServices(): void
    {
        foreach (['CustomMcpToolGeneratorService', 'DynamicMcpToolRegistry', 'McpToolLibraryService'] as $service) {
            $this->putStub(
                app_path("Services/{$service}.php"),
                $this->stubPath("app/Services/{$service}.stub")
            );
        }

        $filesystem = new Filesystem;
        $source = $this->stubPath('app/Services/Mcp');

        collect($filesystem->allFiles($source))
            ->each(function (SplFileInfo $file) use ($filesystem): void {
                $target = app_path('Services/Mcp/'.Str::replaceLast('.stub', '.php', $file->getFilename()));
                $this->putFile($target, $filesystem->get($file->getPathname()));
            });
    }

    protected function publishMcpTools(): void
    {
        $filesystem = new Filesystem;
        $source = $this->stubPath('app/Mcp/Tools');

        collect($filesystem->allFiles($source))
            ->each(function (SplFileInfo $file) use ($filesystem): void {
                $target = app_path('Mcp/Tools/'.Str::replaceLast('.stub', '.php', $file->getFilename()));
                $this->putFile($target, $filesystem->get($file->getPathname()));
            });
    }

    protected function publishMcpServer(): void
    {
        $this->putStub(
            app_path('Mcp/Servers/BoilerplateServer.php'),
            $this->stubPath('app/Mcp/Servers/BoilerplateServer.stub')
        );
    }

    protected function publishControllers(): void
    {
        $this->putStub(
            app_path('Http/Controllers/McpToolController.php'),
            $this->stubPath('app/Http/Controllers/McpToolController.stub')
        );
    }

    protected function publishMigrations(): void
    {
        $stubs = [
            '000001_create_mcp_projects_table' => 'create_mcp_projects_table.stub',
            '000002_create_mcp_audit_logs_table' => 'create_mcp_audit_logs_table.stub',
            '000003_create_mcp_tools_table' => 'create_mcp_tools_table.stub',
            '000004_add_mcp_tool_definition_columns_to_mcp_tools_table' => 'add_mcp_tool_definition_columns_to_mcp_tools_table.stub',
            '000005_change_mcp_tools_handler_type_to_tinyinteger' => 'change_mcp_tools_handler_type_to_tinyinteger.stub',
            '000006_add_tool_class_to_mcp_tools_table' => 'add_tool_class_to_mcp_tools_table.stub',
            '000007_change_mcp_tools_description_to_longtext' => 'change_mcp_tools_description_to_longtext.stub',
            '000008_add_custom_mcp_tool_context_columns_to_mcp_tools_table' => 'add_custom_mcp_tool_context_columns_to_mcp_tools_table.stub',
        ];

        $timestamp = date('Y_m_d_His');

        foreach ($stubs as $suffix => $stub) {
            $path = database_path("migrations/{$timestamp}_{$suffix}.php");
            if (file_exists($path) && ! $this->option('force')) {
                $this->warn('Skipped (exists): '.$path);
                $this->skipped++;

                continue;
            }

            $this->putStub($path, $this->stubPath('database/migrations/'.$stub));
        }
    }

    protected function publishViews(): void
    {
        $filesystem = new Filesystem;
        $source = $this->stubPath('resources/views/mcp_tools');

        collect($filesystem->allFiles($source))
            ->each(function (SplFileInfo $file) use ($filesystem): void {
                $target = resource_path('views/mcp_tools/'.Str::replaceLast('.stub', '.php', $file->getFilename()));
                $this->putFile($target, $filesystem->get($file->getPathname()));
            });
    }

    protected function publishCustomToolStubs(): void
    {
        $filesystem = new Filesystem;
        $source = $this->stubPath('stubs/mcp');

        collect($filesystem->allFiles($source))
            ->each(function (SplFileInfo $file) use ($filesystem): void {
                $relative = str_replace('\\', '/', $file->getRelativePathname());
                $target = base_path('stubs/mcp/'.$relative);
                $this->putFile($target, $filesystem->get($file->getPathname()));
            });
    }

    protected function publishAiRoutes(): void
    {
        $this->putStub(base_path('routes/ai.php'), $this->stubPath('routes/ai.stub'));
    }

    protected function publishWebRoutes(): void
    {
        $webPath = base_path('routes/web.php');
        if (! file_exists($webPath)) {
            $this->warn('routes/web.php not found — add MCP tool routes manually from stubs/build/web_mcp_routes.stub');

            return;
        }

        if (str_contains(file_get_contents($webPath), "mcpTools.index")) {
            $this->comment('Already in routes/web.php: MCP tool routes');

            return;
        }

        $routesStub = trim(file_get_contents($this->stubPath('build/web_mcp_routes.stub')));
        $contents = file_get_contents($webPath);

        if (! str_contains($contents, 'use App\Http\Controllers\McpToolController;')) {
            $contents = preg_replace(
                '/(use Illuminate\\\\Support\\\\Facades\\\\Route;)/',
                "$1\nuse App\\Http\\Controllers\\McpToolController;",
                $contents,
                1
            ) ?? $contents;
        }

        if (preg_match('/Route::middleware\(\[[^\]]*checkIfAdmin[^\]]*\]\)->group\(function \(\) \{/', $contents)) {
            $contents = preg_replace(
                '/(Route::middleware\(\[[^\]]*checkIfAdmin[^\]]*\]\)->group\(function \(\) \{)/',
                "$1\n".$routesStub,
                $contents,
                1
            );

            if ($contents !== null) {
                file_put_contents($webPath, $contents);
                $this->line('Added MCP tool routes to routes/web.php');
                $this->published++;

                return;
            }
        }

        $this->warn('Could not auto-merge MCP routes into routes/web.php — add manually from stubs/build/web_mcp_routes.stub');
    }

    protected function publishTranslations(): void
    {
        $messagesPath = lang_path('en/messages.php');
        if (! file_exists($messagesPath)) {
            $this->warn('lang/en/messages.php not found — merge translations manually from stubs/build/mcp_messages.stub');

            return;
        }

        if (str_contains(file_get_contents($messagesPath), "'mcp_tools_list_page_title'")) {
            $this->comment('Already in lang/en/messages.php: MCP translations');

            return;
        }

        $mcpMessages = trim(file_get_contents($this->stubPath('build/mcp_messages.stub')));
        $contents = file_get_contents($messagesPath);

        $updated = preg_replace(
            '/\n];\s*\z/',
            "\n".$mcpMessages."\n];",
            $contents,
            1
        );

        if ($updated === null) {
            $this->warn('Could not merge MCP translations into lang/en/messages.php');

            return;
        }

        file_put_contents($messagesPath, $updated);
        $this->line('Merged MCP translations into lang/en/messages.php');
        $this->published++;
    }

    protected function publishBootstrapMiddleware(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');
        if (! file_exists($bootstrapPath)) {
            $this->warn('bootstrap/app.php not found — register verifyMcpToken middleware manually');

            return;
        }

        $contents = file_get_contents($bootstrapPath);
        $changed = false;

        if (! str_contains($contents, 'use App\Http\Middleware\VerifyMcpToken;')) {
            $contents = preg_replace(
                '/(use Illuminate\\\\Foundation\\\\Configuration\\\\Middleware;)/',
                "use App\\Http\\Middleware\\VerifyMcpToken;\n$1",
                $contents,
                1
            ) ?? $contents;
            $changed = true;
        }

        if (! str_contains($contents, "'verifyMcpToken'")) {
            $contents = preg_replace(
                "/(\$middleware->alias\(\[\n)/",
                "$1            'verifyMcpToken' => VerifyMcpToken::class,\n",
                $contents,
                1
            );

            if ($contents !== null) {
                $changed = true;
            }
        }

        if (! $changed) {
            $this->comment('Already in bootstrap/app.php: verifyMcpToken middleware');

            return;
        }

        file_put_contents($bootstrapPath, $contents);
        $this->line('Registered verifyMcpToken middleware in bootstrap/app.php');
        $this->published++;
    }
}
