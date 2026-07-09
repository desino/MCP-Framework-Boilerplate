<?php

namespace Desino\McpBoilerplate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McpAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code',
        'tool',
        'input',
        'status',
        'error',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'input' => 'array',
    ];
}
