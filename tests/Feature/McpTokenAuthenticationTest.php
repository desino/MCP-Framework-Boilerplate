<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function mcpInitializePayload(): array
{
    return [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'initialize',
        'params' => [
            'protocolVersion' => '2024-11-05',
            'capabilities' => [],
            'clientInfo' => [
                'name' => 'pest',
                'version' => '1.0.0',
            ],
        ],
    ];
}

it('rejects MCP requests without a bearer token', function () {
    config(['mcp-boilerplate.mcp_api_token' => 'secret-token']);

    $response = $this->postJson('/mcp', mcpInitializePayload());

    $response->assertUnauthorized()
        ->assertJsonPath('error.message', 'Unauthorized MCP request.');
});

it('rejects MCP requests with an invalid bearer token', function () {
    config(['mcp-boilerplate.mcp_api_token' => 'secret-token']);

    $response = $this->postJson('/mcp', mcpInitializePayload(), [
        'Authorization' => 'Bearer wrong-token',
    ]);

    $response->assertUnauthorized();
});

it('rejects MCP requests when the API token is not configured', function () {
    config(['mcp-boilerplate.mcp_api_token' => null]);

    $response = $this->postJson('/mcp', mcpInitializePayload(), [
        'Authorization' => 'Bearer secret-token',
    ]);

    $response->assertStatus(503)
        ->assertJsonPath('error.message', 'MCP API token is not configured.');
});

it('allows MCP requests with a valid bearer token', function () {
    config(['mcp-boilerplate.mcp_api_token' => 'secret-token']);

    $response = $this->postJson('/mcp', mcpInitializePayload(), [
        'Authorization' => 'Bearer secret-token',
    ]);

    $response->assertSuccessful();
});
