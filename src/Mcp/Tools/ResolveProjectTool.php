<?php

namespace Desino\McpBoilerplate\Mcp\Tools;

use Desino\McpBoilerplate\Services\Mcp\AuditLogger;
use Desino\McpBoilerplate\Services\Mcp\ProjectResolver;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

#[IsReadOnly]
#[Description('Resolve project details from a unique project code.')]
class ResolveProjectTool extends SupportTool
{
    protected string $name = 'support.resolve_project';

    public function handle(Request $request, ProjectResolver $resolver, AuditLogger $audit): Response
    {
        $input = $request->validate([
            'project_code' => ['required', 'string'],
        ]);

        try {
            $project = $resolver->resolve($input['project_code']);

            $data = [
                'code' => $project->code,
                'name' => $project->name,
                'github_repo' => $project->github_repo,
                'github_branch' => $project->github_branch,
                'db_connection' => [
                    'driver' => $project->db_connection_diver,
                    'host' => $project->db_connection_host,
                    'port' => $project->db_connection_port,
                    'database' => $project->db_connection_database,
                ],
                'pmtool_initiative_id' => $project->pmtool_initiative_id,
                'instructions' => $project->instructions,
            ];

            $audit->success($this->name, $input);

            return $this->json($data);
        } catch (Throwable $e) {
            $audit->failure($this->name, $input, $e);

            return Response::error($e->getMessage());
        }
    }

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'project_code' => $schema->string()
                ->description('Unique project code, e.g. CLP001.')
                ->required(),
        ];
    }
}
