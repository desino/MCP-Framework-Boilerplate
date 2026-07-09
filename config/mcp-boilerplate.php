<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MCP Endpoint
    |--------------------------------------------------------------------------
    */
    'mcp_path' => env('MCP_PATH', '/mcp'),
    'mcp_api_token' => env('MCP_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Admin UI Routes
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('MCP_ROUTE_PREFIX', ''),
    'admin_middleware' => ['auth', 'checkIfAdmin'],
    'route_name_prefix' => 'mcpTools.',

    /*
    |--------------------------------------------------------------------------
    | Host Application Helpers
    |--------------------------------------------------------------------------
    |
    | When using desino/boilerplate, leave these defaults. Override if your app
    | provides equivalent redirect/validation helpers under different classes.
    |
    */
    'redirect_helper' => env('MCP_REDIRECT_HELPER', \App\Services\AppMiscService::class),
    'layout_view' => env('MCP_LAYOUT_VIEW', 'layouts.app'),

    /*
    |--------------------------------------------------------------------------
    | Custom MCP Tool Generation
    |--------------------------------------------------------------------------
    |
    | Custom tools are generated into the host application so they can be
    | edited and version-controlled alongside project code.
    |
    */
    'custom_tools' => [
        'namespace' => env('MCP_CUSTOM_TOOLS_NAMESPACE', 'App\\Mcp\\Tools'),
        'path' => env('MCP_CUSTOM_TOOLS_PATH', null),
        'support_tool_class' => \Desino\McpBoilerplate\Mcp\Tools\SupportTool::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub Integration
    |--------------------------------------------------------------------------
    */
    'github_api_url' => env('MCP_GITHUB_API_URL', 'https://api.github.com'),
    'github_timeout' => (int) env('MCP_GITHUB_TIMEOUT', 20),

    /*
    |--------------------------------------------------------------------------
    | PM Tool Integration
    |--------------------------------------------------------------------------
    */
    'pmtool' => [
        'base_url' => env('PM_TOOL_API_URL', 'https://pmtool.local'),
        'api_key' => env('PM_TOOL_API_KEY'),
        'timeout' => (int) env('PM_TOOL_API_TIMEOUT', 20),
        'verify' => filter_var(env('PM_TOOL_API_VERIFY', true), FILTER_VALIDATE_BOOLEAN),
    ],
];
