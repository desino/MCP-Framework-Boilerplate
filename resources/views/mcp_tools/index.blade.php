@extends(config('mcp-boilerplate.layout_view', 'layouts.app'))

@section('customscript')
<script>
    jQuery(document).ready(function($) {
        jQuery(document).on('click', '.deactivate_mcp_tool', function(event) {
            event.preventDefault();
            var eleId = parseInt(jQuery(this).attr('id').split('deactivate_mcp_tool_')[1]);
            if (eleId > 0) {
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deactivateConfirmModal'));
                modal.show();
                jQuery('#deactivate_form #deactivate_id').val(eleId);
            }
        });

        jQuery(document).on('click', '#deactivate_confirm_modal_submit_btn', function(event) {
            event.preventDefault();
            jQuery('#loading_screen').show();
            jQuery('form#deactivate_form').submit();
        });

        jQuery(document).on('click', '.activate_mcp_tool', function(event) {
            event.preventDefault();
            var eleId = parseInt(jQuery(this).attr('id').split('activate_mcp_tool_')[1]);
            if (eleId > 0) {
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('activateConfirmModal'));
                modal.show();
                jQuery('#activate_form #activate_id').val(eleId);
            }
        });

        jQuery(document).on('click', '#activate_confirm_modal_submit_btn', function(event) {
            event.preventDefault();
            jQuery('#loading_screen').show();
            jQuery('form#activate_form').submit();
        });

        jQuery(document).on('click', '.delete_mcp_tool', function(event) {
            event.preventDefault();
            var eleId = parseInt(jQuery(this).attr('id').split('delete_mcp_tool_')[1]);
            if (eleId > 0) {
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal'));
                modal.show();
                jQuery('#delete_form #delete_id').val(eleId);
            }
        });

        jQuery(document).on('click', '#delete_confirm_modal_submit_btn', function(event) {
            event.preventDefault();
            jQuery('#loading_screen').show();
            jQuery('form#delete_form').submit();
        });

        jQuery(document).on('click', '.show_mcp_tool_details', function(event) {
            event.preventDefault();
            const details = jQuery(this).data('details');

            jQuery('#details_id').text(details.id ?? '');
            jQuery('#details_name').text(details.name ?? '');
            jQuery('#details_description').text(details.description ?? '');
            jQuery('#details_type').text(details.type ?? '');
            jQuery('#details_tool_class').text(details.tool_class ?? '');
            jQuery('#details_title').text(details.title ?? '{{ __('messages.general_text_na') }}');
            jQuery('#details_input_schema').text(details.input_schema ?? '');
            jQuery('#details_output_schema').text(details.output_schema ?? '');
            jQuery('#details_status').text(details.status ?? '');

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailsModal'));
            modal.show();
        });

        jQuery(document).on('click', '#mcp_tools_index_filter_clear_btn', function(event) {
            event.preventDefault();
            jQuery('input[name="mcp_tools_index_filter_statuses[]"], #mcp_tools_index_filter_keyword').val('');
            jQuery('#loading_screen').show();
            jQuery('#mcp_tools_index_filter_submit_btn').trigger('click');
        });
    });
</script>
@endsection

@section('content')
<div class="row g-0 align-items-center">
    <div class="col-12">
        <div class="card border-0 pt-3">
            <div class="card-header bg-transparent border-0 row align-items-center p-0">
                <div class="col-12 col-md-9 col-lg-10">
                    <h1 class="text-app_dblue">
                        {{ $page_title }}
                    </h1>
                    <div class="my-2 small fst-italic clearfix text-app_lblue">
                        {!! __('mcp-boilerplate::messages.mcp_tools_list_page_desc') !!}
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-2">
                    <a class="btn btn-app_dblue border-0 w-100" href="{{ route('mcpTools.create') }}" role="button">
                        <i class="fa-solid fa-plus"></i> {{ __('mcp-boilerplate::messages.mcp_tools_list_create_btn_text') }}
                    </a>
                </div>
            </div>
            <div class="card-body border-0 px-0">
                <div class="clearfix py-4">
                    <form action="{{ route('mcpTools.index') }}" class="needs-validation" method="POST" novalidate name="mcp_tools_index_filter_form" id="mcp_tools_index_filter_form">
                        <div class="row w-100 g-0 align-items-end">
                            <div class="col-12 col-md-12 col-lg-4 px-1">
                                <div class="w-100 my-1">
                                    @foreach ($mcpToolStatuses as $eachMcpToolStatus)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="mcp_tools_index_filter_status_{{ $eachMcpToolStatus }}" name="mcp_tools_index_filter_statuses[]" value="{{ $eachMcpToolStatus }}" @checked(in_array($eachMcpToolStatus, $mcpToolsIndexFilterStatuses))>
                                            <label class="form-check-label" for="mcp_tools_index_filter_status_{{ $eachMcpToolStatus }}">
                                                <small class="fw-bold">{{ __('messages.general_record_status'.$eachMcpToolStatus.'_text') }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12 col-md-8 col-lg-6 px-1">
                                <div class="w-100 my-1">
                                    <input class="form-control" type="text" name="mcp_tools_index_filter_keyword" id="mcp_tools_index_filter_keyword" value="{{ $mcpToolsIndexFilterKeyword }}" placeholder="{{ __('mcp-boilerplate::messages.mcp_tools_list_filter_keyword_text') }}">
                                </div>
                            </div>
                            <div class="col-6 col-md-2 col-lg-1 px-1">
                                <div class="w-100 my-1">
                                    <button type="submit" class="btn btn-app_dblue w-100 border-0" id="mcp_tools_index_filter_submit_btn" name="mcp_tools_index_filter_submit_btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 col-lg-1 px-1">
                                <div class="w-100 my-1">
                                    <button class="btn btn-dark w-100 border-0" id="mcp_tools_index_filter_clear_btn" name="mcp_tools_index_filter_clear_btn">
                                        <i class="fas fa-eraser"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>

                <div class="w-100">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-none d-md-block active border-0 bg-app_lblue">
                            <div class="row align-items-md-center">
                                <div class="col-md-3 col-lg-3 col-xl-3 text-start">
                                    <small class="fw-bold fst-italic">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_name_text') }}</small>
                                </div>
                                <div class="col-md-3 col-lg-3 col-xl-3 text-start">
                                    <small class="fw-bold fst-italic">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_description_text') }}</small>
                                </div>
                                <div class="col-md-2 col-lg-2 col-xl-2 text-start">
                                    <small class="fw-bold fst-italic">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_handler_type_text') }}</small>
                                </div>
                                <div class="col-md-2 col-lg-2 col-xl-2 text-start">
                                    <small class="fw-bold fst-italic">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_status_text') }}</small>
                                </div>
                                <div class="col-md-2 col-lg-2 col-xl-2 text-end">
                                    <small class="fw-bold fst-italic">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_actions_text') }}</small>
                                </div>
                            </div>
                        </li>

                        @forelse($mcpTools as $eachMcpTool)
                            @php
                                $detailsPayload = [
                                    'id' => $eachMcpTool->id,
                                    'name' => $eachMcpTool->name,
                                    'description' => $eachMcpTool->description,
                                    'type' => $eachMcpTool->handlerTypeLabel(),
                                    'tool_class' => $eachMcpTool->tool_class,
                                    'title' => $eachMcpTool->title,
                                    'input_schema' => json_encode($eachMcpTool->input_schema ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                    'output_schema' => json_encode($eachMcpTool->output_schema ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                    'status' => __('messages.general_record_status'.$eachMcpTool->status.'_text'),
                                ];
                            @endphp
                            <li id="eachmcptool_{{ $eachMcpTool->id }}" class="eachmcptool @if($eachMcpTool->isStatusInactive()) list-group-item-secondary @endif list-group-item border border-app_lblue @if(!$loop->first) border-top-0 @endif">
                                <div class="row align-items-md-center">
                                    <div class="col-12 col-md-3 col-lg-3 col-xl-3 text-start fw-bold text-app_dblue">
                                        {{ $eachMcpTool->name }}
                                    </div>
                                    <div class="col-12 col-md-3 col-lg-3 col-xl-3 text-start small">
                                        <a href="JavaScript:void(0);" class="show_mcp_tool_details text-app_dblue fw-bold" data-details='@json($detailsPayload)'>
                                            {{ __('mcp-boilerplate::messages.mcp_tools_list_show_details_link_text') }}
                                        </a>
                                    </div>
                                    <div class="col-6 col-md-2 col-lg-2 col-xl-2 text-start small">
                                        {{ $eachMcpTool->handlerTypeLabel() }}
                                    </div>
                                    <div class="col-6 col-md-2 col-lg-2 col-xl-2 text-start">
                                        <small class="badge @if($eachMcpTool->isStatusActive()) bg-success @else bg-secondary @endif text-white fw-bold">
                                            {{ __('messages.general_record_status'.$eachMcpTool->status.'_text') }}
                                        </small>
                                    </div>
                                    <div class="col-12 col-md-2 col-lg-2 col-xl-2 text-md-end text-start">
                                        @if ($eachMcpTool->isStatusActive())
                                            <a class="me-1 text-app_dblue deactivate_mcp_tool" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" title="{{ __('mcp-boilerplate::messages.mcp_tools_list_action_deactivate_tooltip_text') }}" data-bs-toggle="tooltip" id="deactivate_mcp_tool_{{ $eachMcpTool->id }}">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </a>
                                        @else
                                            <a class="me-1 text-danger activate_mcp_tool" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" title="{{ __('mcp-boilerplate::messages.mcp_tools_list_action_activate_tooltip_text') }}" data-bs-toggle="tooltip" id="activate_mcp_tool_{{ $eachMcpTool->id }}">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                            </a>
                                        @endif
                                        <a class="me-1 text-app_dblue" href="{{ route('mcpTools.edit', ['id' => $eachMcpTool->id]) }}" style="text-decoration: none;" data-bs-toggle="tooltip" title="{{ __('mcp-boilerplate::messages.mcp_tools_list_action_edit_btn_tooltip_text') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a class="me-1 text-danger delete_mcp_tool" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" title="{{ __('mcp-boilerplate::messages.mcp_tools_list_action_delete_btn_tooltip_text') }}" data-bs-toggle="tooltip" id="delete_mcp_tool_{{ $eachMcpTool->id }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border border-app_lblue border-left-0 border-right-0 border-app_lblue">
                                <div class="row g-0">
                                    <div class="col-12 text-center small">
                                        <small class="fw-bold">{{ __('messages.general_no_results_text') }}</small>
                                    </div>
                                </div>
                            </li>
                        @endforelse

                        @if ($mcpTools->hasPages())
                            <li class="list-group-item border-0">
                                <div class="row g-0">
                                    <div class="col-12 text-center">
                                        {{ $mcpTools->links() }}
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>

                <form action="{{ route('mcpTools.activate') }}" id="activate_form" name="activate_form" method="POST">
                    <input type="hidden" name="activate_id" id="activate_id" value="">
                    @csrf
                </form>
                <form action="{{ route('mcpTools.deactivate') }}" id="deactivate_form" name="deactivate_form" method="POST">
                    <input type="hidden" name="deactivate_id" id="deactivate_id" value="">
                    @csrf
                </form>
                <form action="{{ route('mcpTools.delete') }}" id="delete_form" name="delete_form" method="POST">
                    <input type="hidden" name="delete_id" id="delete_id" value="">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="activateConfirmModal" tabindex="-1" role="dialog" aria-labelledby="activateConfirmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-danger border-0 py-2 justify-content-center">
                <h5 class="modal-title font-italic" id="activateConfirmModalTitle">{{ __('mcp-boilerplate::messages.mcp_tools_list_activate_confirm_modal_title') }}</h5>
            </div>
            <div class="modal-body">
                <div class="w-100 fw-bold">{{ __('mcp-boilerplate::messages.mcp_tools_list_activate_confirm_modal_body_text') }}</div>
            </div>
            <div class="modal-footer border-0 p-0 justify-content-center">
                <div class="row w-100 g-1">
                    <div class="col-10">
                        <a class="btn btn-app_dblue border-0 w-100" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" id="activate_confirm_modal_submit_btn">
                            {{ __('mcp-boilerplate::messages.mcp_tools_list_activate_confirm_modal_cnf_btn_text') }}
                        </a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger border-0 w-100" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deactivateConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deactivateConfirmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-danger border-0 py-2 justify-content-center">
                <h5 class="modal-title font-italic" id="deactivateConfirmModalTitle">{{ __('mcp-boilerplate::messages.mcp_tools_list_deactivate_confirm_modal_title') }}</h5>
            </div>
            <div class="modal-body">
                <div class="w-100 fw-bold">{{ __('mcp-boilerplate::messages.mcp_tools_list_deactivate_confirm_modal_body_text') }}</div>
            </div>
            <div class="modal-footer border-0 p-0 justify-content-center">
                <div class="row w-100 g-1">
                    <div class="col-10">
                        <a class="btn btn-app_dblue border-0 w-100" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" id="deactivate_confirm_modal_submit_btn">
                            {{ __('mcp-boilerplate::messages.mcp_tools_list_deactivate_confirm_modal_cnf_btn_text') }}
                        </a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger border-0 w-100" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-danger border-0 py-2 justify-content-center">
                <h5 class="modal-title font-italic" id="deleteConfirmModalTitle">{{ __('mcp-boilerplate::messages.mcp_tools_list_delete_confirm_modal_title') }}</h5>
            </div>
            <div class="modal-body">
                <div class="w-100 fw-bold">{{ __('mcp-boilerplate::messages.mcp_tools_list_delete_confirm_modal_body_text') }}</div>
            </div>
            <div class="modal-footer border-0 p-0 justify-content-center">
                <div class="row w-100 g-1">
                    <div class="col-10">
                        <a class="btn btn-app_dblue border-0 w-100" href="JavaScript:void(0);" tabindex="0" style="text-decoration: none;" id="delete_confirm_modal_submit_btn">
                            {{ __('mcp-boilerplate::messages.mcp_tools_list_delete_confirm_modal_cnf_btn_text') }}
                        </a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger border-0 w-100" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-app_dblue border-0 py-2 justify-content-center">
                <h5 class="modal-title font-italic" id="detailsModalTitle">{{ __('mcp-boilerplate::messages.mcp_tools_list_details_modal_title') }}</h5>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_details_field_id_text') }}</dt>
                    <dd class="col-sm-9" id="details_id"></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_name_text') }}</dt>
                    <dd class="col-sm-9" id="details_name"></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_details_field_title_text') }}</dt>
                    <dd class="col-sm-9" id="details_title"></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_description_text') }}</dt>
                    <dd class="col-sm-9"><pre class="mb-0 small bg-light p-2 border rounded" id="details_description"></pre></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_handler_type_text') }}</dt>
                    <dd class="col-sm-9" id="details_type"></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tool_form_field_tool_class_text') }}</dt>
                    <dd class="col-sm-9"><code id="details_tool_class"></code></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_column_status_text') }}</dt>
                    <dd class="col-sm-9" id="details_status"></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_details_field_input_schema_text') }}</dt>
                    <dd class="col-sm-9"><pre class="mb-0 small bg-light p-2 border rounded" id="details_input_schema"></pre></dd>

                    <dt class="col-sm-3">{{ __('mcp-boilerplate::messages.mcp_tools_list_details_field_output_schema_text') }}</dt>
                    <dd class="col-sm-9"><pre class="mb-0 small bg-light p-2 border rounded" id="details_output_schema"></pre></dd>
                </dl>
            </div>
            <div class="modal-footer border-0 p-0 justify-content-center">
                <button type="button" class="btn btn-dark border-0 w-100" data-bs-dismiss="modal">
                    {{ __('mcp-boilerplate::messages.mcp_tools_list_details_close_btn_text') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
