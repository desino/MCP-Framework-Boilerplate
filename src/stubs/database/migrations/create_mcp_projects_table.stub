<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('github_repo')->comment('owner/repo, e.g. desino/client-portal'); // owner/repo, e.g. desino/client-portal
            $table->string('github_branch')->default('main')->comment('GitHub branch');
            $table->text('github_token')->nullable()->comment('GitHub PAT or fine-grained token'); // encrypted by model mutator
            $table->string('db_connection_diver')->nullable()->comment('Database driver');
            $table->string('db_connection_host')->nullable()->comment('Database host');
            $table->string('db_connection_port')->nullable()->comment('Database port');
            $table->string('db_connection_database')->nullable()->comment('Database name');
            $table->string('db_connection_username')->nullable()->comment('Database username');
            $table->string('db_connection_password')->nullable()->comment('Database password');
            $table->bigInteger('pmtool_initiative_id')->nullable()->comment('PM tool initiative ID');
            $table->boolean('active')->default(true)->comment('Active status');
            $table->longText('instructions')->nullable()->comment('instruction set for AI agent.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_projects');
    }
};
