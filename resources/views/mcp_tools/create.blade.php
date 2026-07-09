@extends(config('mcp-boilerplate.layout_view', 'layouts.app'))

@section('customscript')
<script>
    jQuery(document).ready(function($) {
        const customSelection = @json(\Desino\McpBoilerplate\Models\McpTool::TOOL_SELECTION_CUSTOM);
        const libraryTools = @json($libraryTools);

        const updateLibraryPreview = function () {
            const selectedClass = jQuery('#tool_selection').val();
            const tool = libraryTools.find(function (item) {
                return item.class === selectedClass;
            });

            if (!tool) {
                jQuery('#library_tool_preview_name').text('');
                jQuery('#library_tool_preview_description').text('');
                return;
            }

            jQuery('#library_tool_preview_name').text(tool.label);
            jQuery('#library_tool_preview_description').text(tool.description);
        };

        const toggleCustomFields = function () {
            const isCustom = jQuery('#tool_selection').val() === customSelection;

            jQuery('#custom_tool_fields').toggleClass('d-none', !isCustom);
            jQuery('#description').prop('required', isCustom);
            jQuery('#library_tool_preview').toggleClass('d-none', isCustom);
            updateLibraryPreview();
        };

        jQuery('#tool_selection').on('change', toggleCustomFields);
        toggleCustomFields();
    });
</script>
@endsection

@section('content')
<div class="row g-0 align-items-center">
    <div class="col-12">
        <form action="{{ route('mcpTools.create') }}" class="needs-validation" method="POST" novalidate name="mcp_tool_create_form" id="mcp_tool_create_form">
            @csrf
            <div class="card border-0 pt-3">
                <div class="card-header bg-transparent border-0 row d-flex align-items-center p-0">
                    <div class="col-12 col-md-9 col-lg-10">
                        <h1 class="text-app_dblue">
                            {{ $page_title }}
                        </h1>
                        <div class="my-2 small fst-italic clearfix text-app_lblue">
                            {!! __('mcp-boilerplate::messages.create_mcp_tool_page_desc') !!}
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
                            <label for="tool_selection" class="fw-bold form-label">
                                {{ __('mcp-boilerplate::messages.mcp_tool_form_field_tool_selection_text') }} <sup><i class="fa-solid fa-asterisk text-danger small fw-bold"></i></sup>
                            </label>
                            <select class="form-select @error('tool_selection') is-invalid @enderror" id="tool_selection" name="tool_selection">
                                <option value="">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_tool_selection_placeholder_text') }}</option>
                                @foreach ($libraryTools as $libraryTool)
                                    <option value="{{ $libraryTool['class'] }}" @selected(old('tool_selection') === $libraryTool['class'])>
                                        {{ $libraryTool['label'] }}
                                    </option>
                                @endforeach
                                <option value="{{ \Desino\McpBoilerplate\Models\McpTool::TOOL_SELECTION_CUSTOM }}" @selected(old('tool_selection') === \Desino\McpBoilerplate\Models\McpTool::TOOL_SELECTION_CUSTOM)>
                                    {{ __('mcp-boilerplate::messages.mcp_tool_form_field_custom_tool_text') }}
                                </option>
                            </select>
                            <small class="text-muted">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_library_tool_help_text') }}</small>
                            @error('tool_selection')
                                <small class="invalid-feedback fw-bold fst-italic text-danger" role="alert">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="name" class="fw-bold form-label">
                                {{ __('mcp-boilerplate::messages.mcp_tool_form_field_name_text') }} <sup><i class="fa-solid fa-asterisk text-danger small fw-bold"></i></sup>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" autocomplete="off" placeholder="example_tool" />
                            <small class="text-muted">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_name_help_text') }}</small>
                            @error('name')
                                <small class="invalid-feedback fw-bold fst-italic text-danger" role="alert">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-3 d-none" id="library_tool_preview">
                            <div class="border border-app_lblue rounded p-3 bg-light">
                                <div class="fw-bold text-app_dblue mb-1" id="library_tool_preview_name"></div>
                                <div class="small" id="library_tool_preview_description"></div>
                            </div>
                        </div>

                        @include('mcp_tools._custom_tool_fields', ['showCustomFields' => false, 'context' => null])
                    </div>
                </div>
                <div class="card-footer bg-transparent p-0 d-grid gap-1">
                    <button class="btn btn-app_dblue fw-bold border-0 text-white" type="submit" style="outline: none;">
                        {{ __('mcp-boilerplate::messages.create_mcp_tool_form_submit_btn_text') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
