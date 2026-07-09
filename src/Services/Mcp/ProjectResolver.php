<?php

namespace Desino\McpBoilerplate\Services\Mcp;

use Desino\McpBoilerplate\Models\McpProject;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectResolver
{
    public function resolve(string $projectCode): McpProject
    {
        $project = McpProject::query()
            ->where('code', strtoupper(trim($projectCode)))
            ->where('active', true)
            ->first();

        if (! $project) {
            throw (new ModelNotFoundException)->setModel(McpProject::class, [$projectCode]);
        }

        return $project;
    }
}
