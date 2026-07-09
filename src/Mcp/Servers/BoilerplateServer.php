<?php

namespace Desino\McpBoilerplate\Mcp\Servers;

use Desino\McpBoilerplate\Services\DynamicMcpToolRegistry;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\ServerContext;

#[Name('MCP Boilerplate Server')]
#[Version('1.0.0')]
#[Instructions(<<<'MARKDOWN'
This server exposes MCP tools managed from the application's MCP Tool manager.
Only tools with an enabled status in the admin UI are available on the /mcp endpoint.
MARKDOWN)]
class BoilerplateServer extends Server
{
    protected array $tools = [];

    protected array $resources = [];

    protected array $prompts = [];

    public function createContext(): ServerContext
    {
        $this->tools = app(DynamicMcpToolRegistry::class)
            ->activeTools()
            ->all();

        return parent::createContext();
    }
}
