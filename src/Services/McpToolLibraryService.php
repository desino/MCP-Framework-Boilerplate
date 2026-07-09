<?php

namespace Desino\McpBoilerplate\Services;

use Desino\McpBoilerplate\Mcp\Tools\ReadFileTool;
use Desino\McpBoilerplate\Mcp\Tools\RecentCommitsTool;
use Desino\McpBoilerplate\Mcp\Tools\ResolveProjectTool;
use Desino\McpBoilerplate\Mcp\Tools\SearchCodeTool;
use Desino\McpBoilerplate\Mcp\Tools\SearchTicketsTool;
use Illuminate\Contracts\Container\Container;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use ReflectionClass;

class McpToolLibraryService
{
    /**
     * @var list<class-string<Tool>>
     */
    public const LIBRARY_TOOLS = [
        ResolveProjectTool::class,
        SearchTicketsTool::class,
        SearchCodeTool::class,
        ReadFileTool::class,
        RecentCommitsTool::class,
    ];

    public function __construct(
        protected Container $container,
    ) {
        //
    }

    /**
     * @return list<class-string<Tool>>
     */
    public function libraryToolClasses(): array
    {
        return self::LIBRARY_TOOLS;
    }

    public function isLibraryToolClass(string $class): bool
    {
        return in_array($class, self::LIBRARY_TOOLS, true);
    }

    /**
     * @return array<int, array{class: class-string<Tool>, name: string, label: string, description: string}>
     */
    public function options(): array
    {
        return collect(self::LIBRARY_TOOLS)
            ->map(fn (string $class): array => $this->describe($class))
            ->values()
            ->all();
    }

    /**
     * @return array{class: class-string<Tool>, name: string, label: string, description: string}
     */
    public function describe(string $class): array
    {
        /** @var Tool $tool */
        $tool = $this->container->make($class);

        return [
            'class' => $class,
            'name' => $tool->name(),
            'label' => $tool->title(),
            'description' => $this->resolveDescription($class, $tool),
        ];
    }

    protected function resolveDescription(string $class, Tool $tool): string
    {
        $reflection = new ReflectionClass($class);
        $attributes = $reflection->getAttributes(Description::class);

        if ($attributes !== []) {
            return $attributes[0]->newInstance()->value;
        }

        return $tool->description();
    }
}
