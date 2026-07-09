<?php

namespace Desino\McpBoilerplate\Services\Mcp;

use Desino\McpBoilerplate\Models\McpProject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PmToolService
{
    public function __construct(
        protected TicketRelevanceFilter $relevanceFilter,
    ) {}

    protected function client()
    {
        $apiKey = config('mcp-boilerplate.pmtool.api_key');

        if (! $apiKey) {
            throw new RuntimeException('PM tool API key (PM_TOOL_API_KEY) is not configured.');
        }

        return Http::withHeaders(['X-MCP-Api-Key' => $apiKey])
            ->acceptJson()
            ->withOptions(['verify' => (bool) config('mcp-boilerplate.pmtool.verify', true)])
            ->timeout(config('mcp-boilerplate.pmtool.timeout'));
    }

    /**
     * Fetch tickets for the project's initiative, optionally limited.
     *
     * @return array<int, array<string, mixed>>
     */
    public function tickets(McpProject $project, ?int $limit = null): array
    {
        $tickets = $this->fetchTickets($project);

        if ($limit !== null) {
            $tickets = $tickets->take($limit);
        }

        return $tickets->values()->all();
    }

    /**
     * Smartly filter API tickets by relevance to a prompt across composed_name,
     * description, release_note and comments.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchTickets(McpProject $project, string $query, int $limit = 10): array
    {
        return $this->relevanceFilter->filter(
            $this->fetchTickets($project, $query)->all(),
            $query,
            $limit,
        );
    }

    /**
     * Fetch and normalise tickets from the PM tool API.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function fetchTickets(McpProject $project, ?string $query = null): Collection
    {
        if (! $project->pmtool_initiative_id) {
            return collect();
        }

        $baseUrl = rtrim((string) config('mcp-boilerplate.pmtool.base_url'), '/');
        $url = "{$baseUrl}/api/mcp_resource/initiatives/{$project->pmtool_initiative_id}/tickets";

        $payload = [];

        if (filled($query)) {
            $keywords = $this->relevanceFilter->keywords($query);
            if ($keywords !== []) {
                $payload['keywords'] = $keywords;
            }
        }

        $response = $this->client()->asJson()->post($url, $payload);

        if ($response->failed()) {
            throw new RuntimeException('PM tool tickets request failed: '.$response->body());
        }

        return collect($response->json('data.tickets', []))
            ->map(fn ($ticket) => [
                'link' => $ticket['link'] ?? null,
                'composed_name' => $ticket['composed_name'] ?? null,
                'description' => $ticket['description'] ?? null,
                'release_note' => $ticket['release_note'] ?? null,
                'status' => $ticket['status'] ?? null,
                'created_at' => $ticket['created_at'] ?? null,
                'comments' => collect($ticket['comments'] ?? [])->map(fn ($comment) => [
                    'comment' => $comment['comment'] ?? null,
                    'posted_at' => $comment['posted_at'] ?? null,
                    'posted_by' => $comment['posted_by'] ?? null,
                ])->all(),
            ])
            ->values();
    }
}
