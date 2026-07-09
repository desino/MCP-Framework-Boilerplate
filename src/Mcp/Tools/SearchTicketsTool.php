<?php

namespace Desino\McpBoilerplate\Mcp\Tools;

use Desino\McpBoilerplate\Services\Mcp\AuditLogger;
use Desino\McpBoilerplate\Services\Mcp\PmToolService;
use Desino\McpBoilerplate\Services\Mcp\ProjectResolver;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

#[IsReadOnly]
#[Description('Smartly filter PM Tool tickets by prompt relevance across composed name, description, release notes and comments.')]
class SearchTicketsTool extends SupportTool
{
    protected string $name = 'pmtool.search_tickets';

    public function handle(
        Request $request,
        ProjectResolver $resolver,
        PmToolService $pmTool,
        AuditLogger $audit,
    ): Response {
        $input = $request->validate([
            'project_code' => ['required', 'string'],
            'query' => ['required', 'string'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        try {
            $project = $resolver->resolve($input['project_code']);
            $limit = (int) ($input['limit'] ?? 10);

            $data = [
                'query' => $input['query'],
                'tickets' => $pmTool->searchTickets($project, $input['query'], $limit),
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
            'query' => $schema->string()
                ->description('Support prompt used to smartly filter tickets by composed name, description, release notes and comments.')
                ->required(),
            'limit' => $schema->integer()
                ->description('Maximum number of tickets to return (default 10, max 50).'),
        ];
    }
}
