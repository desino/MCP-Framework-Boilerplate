<?php

namespace Desino\McpBoilerplate\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McpProject extends Model
{
    use HasFactory;

    /**
     * Default instruction set used when a project has no instructions configured.
     *
     * @var array<string, string>
     */
    public const DEFAULT_INSTRUCTIONS = [
        'framework' => 'Laravel',
        'analysis_depth' => 'strong',
        'output_format' => 'root cause, evidence, affected files, confidence, suggested fix',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected function githubToken(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? decrypt($value) : null,
            set: fn (?string $value) => $value ? encrypt($value) : null,
        );
    }

    protected function instructions(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => filled($value)
                ? json_decode($value, true)
                : self::DEFAULT_INSTRUCTIONS,
            set: fn ($value) => $value === null ? null : json_encode($value),
        );
    }
}
