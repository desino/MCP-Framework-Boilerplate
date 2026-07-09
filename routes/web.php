<?php

use Desino\McpBoilerplate\Http\Controllers\McpToolController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'mcp_tools', [McpToolController::class, 'index'])->name('index');
Route::match(['get', 'post'], 'mcp_tools/create', [McpToolController::class, 'create'])->name('create');
Route::match(['get', 'post'], 'mcp_tools/{id}/edit', [McpToolController::class, 'edit'])->name('edit');
Route::post('mcp_tools/activate', [McpToolController::class, 'activate'])->name('activate');
Route::post('mcp_tools/deactivate', [McpToolController::class, 'deactivate'])->name('deactivate');
Route::post('mcp_tools/delete', [McpToolController::class, 'delete'])->name('delete');
