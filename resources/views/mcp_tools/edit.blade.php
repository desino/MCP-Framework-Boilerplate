@php
    use Desino\McpBoilerplate\Data\CustomMcpToolContext;

    $context = CustomMcpToolContext::fromModel($mcpTool);
@endphp

@extends(config('mcp-boilerplate.layout_view', 'layouts.app'))

@section('content')
<div class="row g-0 align-items-center">
    <div class="col-12">
        <form action="{{ route('mcpTools.edit', ['id' => $mcpTool->id]) }}" class="needs-validation" method="POST" novalidate name="mcp_tool_edit_form" id="mcp_tool_edit_form">
            @csrf
            <input type="hidden" name="tool_selection" value="{{ $mcpTool->isHandlerTypeCustom() ? \Desino\McpBoilerplate\Models\McpTool::TOOL_SELECTION_CUSTOM : $mcpTool->tool_class }}">

            <div class="card border-0 pt-3">
                <div class="card-header bg-transparent border-0 row d-flex align-items-center p-0">
                    <div class="col-12 col-md-9 col-lg-10">
                        <h1 class="text-app_dblue">
                            {{ $page_title }}
                        </h1>
                        <div class="my-2 small fst-italic clearfix text-app_lblue">
                            {!! __('mcp-boilerplate::messages.edit_mcp_tool_page_desc') !!}
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <a class="btn btn-dark border-0 w-100" href="{{ route('mcpTools.index') }}" role="button">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 px-0">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="fw-bold form-label">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_name_text') }}</label>
                            <input type="text" class="form-control" value="{{ $mcpTool->name }}" readonly disabled />
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label class="fw-bold form-label">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_handler_type_text') }}</label>
                            <input type="text" class="form-control" value="{{ $mcpTool->handlerTypeLabel() }}" readonly disabled />
                        </div>

                        @if ($mcpTool->isHandlerTypeLibrary())
                            <div class="col-12 col-md-6 mb-3">
                                <label class="fw-bold form-label">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_tool_class_text') }}</label>
                                <input type="text" class="form-control font-monospace small" value="{{ $mcpTool->tool_class }}" readonly disabled />
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold form-label">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_description_text') }}</label>
                                <textarea class="form-control" rows="4" readonly disabled>{{ $mcpTool->description }}</textarea>
                                <small class="text-muted">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_library_tool_help_text') }}</small>
                            </div>
                        @else
                            @include('mcp_tools._custom_tool_fields', ['showCustomFields' => true, 'context' => $context])
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent p-0 d-grid gap-1">
                    @if ($mcpTool->isHandlerTypeCustom())
                        <button class="btn btn-app_dblue fw-bold border-0 text-white" type="submit" style="outline: none;">
                            {{ __('mcp-boilerplate::messages.edit_mcp_tool_form_submit_btn_text') }}
                        </button>
                    @else
                        <a class="btn btn-dark fw-bold border-0 text-white" href="{{ route('mcpTools.index') }}" style="outline: none;">
                            {{ __('mcp-boilerplate::messages.mcp_tools_list_details_close_btn_text') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
