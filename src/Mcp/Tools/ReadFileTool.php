<?php

namespace Desino\McpBoilerplate\Mcp\Tools;

use Desino\McpBoilerplate\Services\Mcp\AuditLogger;
use Desino\McpBoilerplate\Services\Mcp\GithubService;
use Desino\McpBoilerplate\Services\Mcp\ProjectResolver;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

#[IsReadOnly]
#[Description('Read one file from the configured GitHub repo/branch.')]
class ReadFileTool extends SupportTool
{
    protected string $name = 'github.read_file';

    public function handle(
        Request $request,
        ProjectResolver $resolver,
        GithubService $github,
        AuditLogger $audit,
    ): Response {
        $input = $request->validate([
            'project_code' => ['required', 'string'],
            'path' => ['required', 'string'],
        ]);

        try {
            $project = $resolver->resolve($input['project_code']);
            $data = $github->readFile($project, $input['path']);

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
            'path' => $schema->string()
                ->description('Repository-relative file path, e.g. app/Models/User.php.')
                ->required(),
        ];
    }
}
