<?php

use Desino\McpBoilerplate\Models\McpTool;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mcp_tools', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->json('input_schema')->nullable()->after('description');
            $table->tinyInteger('handler_type')->default(McpTool::getHandlerTypeLibrary())->after('input_schema');
            $table->json('handler_config')->nullable()->after('handler_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mcp_tools', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'input_schema',
                'handler_type',
                'handler_config',
            ]);
        });
    }
};
