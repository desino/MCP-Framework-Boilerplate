<?php

namespace Desino\McpBoilerplate\Services\Mcp;

use Desino\McpBoilerplate\Models\McpAuditLog;
use Illuminate\Http\Request;
use Throwable;

class AuditLogger
{
    public function success(string $tool, array $input, ?Request $request = null): void
    {
        $this->write($tool, $input, 'success', null, $request);
    }

    public function failure(string $tool, array $input, Throwable|string $error, ?Request $request = null): void
    {
        $this->write($tool, $input, 'failure', $error instanceof Throwable ? $error->getMessage() : $error, $request);
    }

    protected function write(string $tool, array $input, string $status, ?string $error, ?Request $request): void
    {
        McpAuditLog::query()->create([
            'project_code' => $input['project_code'] ?? null,
            'tool' => $tool,
            'input' => $input,
            'status' => $status,
            'error' => $error,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
