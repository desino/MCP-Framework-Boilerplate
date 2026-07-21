<?php

namespace Desino\McpBoilerplate;

use Desino\McpBoilerplate\Commands\MakeMcpBoilerplateCommand;
use Illuminate\Support\ServiceProvider;

class McpBoilerplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeMcpBoilerplateCommand::class,
            ]);
        }
    }
}
