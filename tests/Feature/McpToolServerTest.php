<?php

use Desino\McpBoilerplate\Data\CustomMcpToolContext;
use Desino\McpBoilerplate\Mcp\Servers\BoilerplateServer;
use Desino\McpBoilerplate\Mcp\Tools\ResolveProjectTool;
use Desino\McpBoilerplate\Models\McpTool;
use Desino\McpBoilerplate\Services\CustomMcpToolGeneratorService;
use Desino\McpBoilerplate\Services\DynamicMcpToolRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Server\Testing\TestResponse;

uses(RefreshDatabase::class);

it('exposes active library tools through the MCP server', function () {
    McpTool::create([
        'name' => 'support.resolve_project',
        'description' => 'Resolve project details from a unique project code.',
        'status' => McpTool::getStatusActive(),
        'handler_type' => McpTool::getHandlerTypeLibrary(),
        'tool_class' => ResolveProjectTool::class,
        'created_at' => now(),
        'created_by' => 1,
    ]);

    $response = BoilerplateServer::tool(ResolveProjectTool::class, [
        'project_code' => 'MISSING',
    ]);

    $response->assertHasErrors();
});

it('generates a custom tool class file with MCP context metadata', function () {
    $generator = app(CustomMcpToolGeneratorService::class);

    $context = new CustomMcpToolContext(
        title: 'Hello World',
        description: 'Returns a greeting for the given name.',
        inputSchema: [
            [
                'name' => 'name',
                'type' => 'string',
                'description' => 'Name to greet.',
                'required' => true,
            ],
        ],
        outputSchema: [
            [
                'name' => 'message',
                'type' => 'string',
                'description' => 'Greeting message.',
                'required' => true,
            ],
        ],
        isReadOnly: true,
        isDestructive: false,
    );

    $generated = $generator->generate('hello_world', $context);

    expect($generated['class'])->toBe('App\\Mcp\\Tools\\HelloWorldTool')
        ->and(file_exists($generated['path']))->toBeTrue();

    $contents = file_get_contents($generated['path']);

    expect($contents)
        ->toContain("#[Title('Hello World')]")
        ->toContain('Returns a greeting for the given name.')
        ->toContain('#[IsReadOnly]')
        ->toContain("'name' => \$schema->string()")
        ->toContain('public function outputSchema(JsonSchema $schema): array')
        ->toContain("'message' => \$schema->string()")
        ->toContain("'name' => ['required', 'string']");

    $generator->deleteGeneratedFile($generated['class']);

    expect(file_exists($generated['path']))->toBeFalse();
});

it('syncs custom tool MCP context into an existing generated file', function () {
    $generator = app(CustomMcpToolGeneratorService::class);

    $generated = $generator->generate('sync_test', new CustomMcpToolContext(
        title: 'Sync Test',
        description: 'Original description.',
        inputSchema: [],
        outputSchema: [],
        isReadOnly: true,
        isDestructive: false,
    ));

    $generator->syncContext($generated['class'], new CustomMcpToolContext(
        title: 'Updated Title',
        description: 'Updated description.',
        inputSchema: [
            [
                'name' => 'query',
                'type' => 'string',
                'description' => 'Search query.',
                'required' => true,
            ],
        ],
        outputSchema: [],
        isReadOnly: false,
        isDestructive: true,
    ));

    $contents = file_get_contents($generated['path']);

    expect($contents)
        ->toContain("#[Title('Updated Title')]")
        ->toContain('Updated description.')
        ->toContain('#[IsDestructive]')
        ->toContain("'query' => \$schema->string()")
        ->not->toContain('#[IsReadOnly]');

    $generator->deleteGeneratedFile($generated['class']);
});

it('loads active registered tools from the database registry', function () {
    McpTool::create([
        'name' => 'support.resolve_project',
        'description' => 'Resolve project details from a unique project code.',
        'status' => McpTool::getStatusActive(),
        'handler_type' => McpTool::getHandlerTypeLibrary(),
        'tool_class' => ResolveProjectTool::class,
        'created_at' => now(),
        'created_by' => 1,
    ]);

    $tools = app(DynamicMcpToolRegistry::class)->activeTools();

    expect($tools)->toHaveCount(1)
        ->and($tools->first())->toBeInstanceOf(ResolveProjectTool::class);
});

it('does not load disabled tools into the MCP registry', function () {
    McpTool::create([
        'name' => 'support.resolve_project',
        'description' => 'Resolve project details from a unique project code.',
        'status' => McpTool::getStatusInactive(),
        'handler_type' => McpTool::getHandlerTypeLibrary(),
        'tool_class' => ResolveProjectTool::class,
        'created_at' => now(),
        'created_by' => 1,
    ]);

    expect(app(DynamicMcpToolRegistry::class)->activeTools())->toHaveCount(0);
});

it('does not expose disabled tools through the MCP server', function () {
    McpTool::create([
        'name' => 'support.resolve_project',
        'description' => 'Resolve project details from a unique project code.',
        'status' => McpTool::getStatusInactive(),
        'handler_type' => McpTool::getHandlerTypeLibrary(),
        'tool_class' => ResolveProjectTool::class,
        'created_at' => now(),
        'created_by' => 1,
    ]);

    /** @var TestResponse $response */
    $response = BoilerplateServer::tool(ResolveProjectTool::class, [
        'project_code' => 'MISSING',
    ]);

    $response->assertHasErrors();
});

it('normalizes custom tool schema json fields', function () {
    $context = CustomMcpToolContext::parseSchemaJson('[{"name":"project_code","type":"string","description":"Project code.","required":true}]', 'input_schema_json');

    expect($context)->toBe([
        [
            'name' => 'project_code',
            'type' => 'string',
            'description' => 'Project code.',
            'required' => true,
        ],
    ]);
});
