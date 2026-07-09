<?php

use Desino\McpBoilerplate\Models\McpTool;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(Schema::getColumnType('mcp_tools', 'handler_type'), ['string', 'varchar'], true)) {
            DB::table('mcp_tools')->where('handler_type', 'text_response')->update(['handler_type' => (string) McpTool::getHandlerTypeLibrary()]);
            DB::table('mcp_tools')->where('handler_type', 'database_query')->update(['handler_type' => '2']);
            DB::table('mcp_tools')->where('handler_type', 'php_class')->update(['handler_type' => (string) McpTool::getHandlerTypeCustom()]);

            Schema::table('mcp_tools', function (Blueprint $table) {
                $table->unsignedTinyInteger('handler_type')->default(McpTool::getHandlerTypeLibrary())->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! in_array(Schema::getColumnType('mcp_tools', 'handler_type'), ['string', 'varchar'], true)) {
            Schema::table('mcp_tools', function (Blueprint $table) {
                $table->string('handler_type', 55)->default('text_response')->change();
            });

            DB::table('mcp_tools')->where('handler_type', (string) McpTool::getHandlerTypeLibrary())->update(['handler_type' => 'text_response']);
            DB::table('mcp_tools')->where('handler_type', '2')->update(['handler_type' => 'database_query']);
            DB::table('mcp_tools')->where('handler_type', (string) McpTool::getHandlerTypeCustom())->update(['handler_type' => 'php_class']);
        }
    }
};
