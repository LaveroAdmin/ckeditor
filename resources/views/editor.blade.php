@include("admin::form._header")

        <style>
            .cke_editor_column, .cke_editor_body{
                border-radius:0.25rem;
                overflow:hidden;
                border-color:#ced4da;
            }
            .cke_focus{
                border-color:#9dcbff;
                box-shadow:0 0 0 .25rem rgba(58,150,255,.25);
            }
            .lavero-ckeditor-wrap {
                position: relative;
            }
            .lavero-ckeditor-actions {
                display: flex;
                justify-content: flex-end;
                gap: 6px;
                margin-bottom: 6px;
            }
            .lavero-ckeditor-wrap.lavero-ckeditor-fullscreen {
                position: fixed;
                inset: 0;
                z-index: 1060;
                background: #ffffff;
                padding: 16px;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .lavero-ckeditor-wrap.lavero-ckeditor-fullscreen .ck-editor {
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                min-height: 0;
            }
            .lavero-ckeditor-wrap.lavero-ckeditor-fullscreen .ck-editor__main {
                flex: 1 1 auto;
                overflow-y: auto;
            }
            .lavero-ckeditor-wrap .ck-editor__editable_inline {
                min-height: 300px;
            }
            .lavero-ckeditor-wrap.lavero-ckeditor-fullscreen .ck-editor__editable_inline {
                min-height: calc(100vh - 140px);
            }
            .lavero-ckeditor-wrap .ck-content pre {
                background: #0f172a !important;
                color: #e2e8f0 !important;
                border: 1px solid #1e293b !important;
                border-radius: 8px !important;
                padding: 14px 16px !important;
                font-family: Consolas, Monaco, "Courier New", monospace !important;
                font-size: 13px !important;
                line-height: 1.55 !important;
                overflow-x: auto;
            }
            .lavero-ckeditor-wrap .ck-content pre code {
                background: transparent !important;
                color: #e2e8f0 !important;
                padding: 0 !important;
                border: 0 !important;
                font-size: inherit !important;
            }
            .lavero-ckeditor-wrap .ck-content pre::after {
                background: #1e293b !important;
                color: #bef264 !important;
                border-radius: 4px;
            }
            .lavero-ckeditor-wrap .ck-content code {
                background: #f1f5f9;
                color: #0f172a;
                border: 1px solid #e2e8f0;
                border-radius: 4px;
                padding: 2px 5px;
                font-size: 0.875em;
            }
        </style>
        <div id="{{ $id }}_wrap" class="lavero-ckeditor-wrap">
            <div class="lavero-ckeditor-actions">
                <button type="button" id="{{ $id }}_media_btn" class="btn btn-xs btn-outline-secondary" title="{{ trans('lavero-admin-ckeditor::ckeditor.media') }}">
                    <i class="icon-image me-1"></i> {{ trans('lavero-admin-ckeditor::ckeditor.media') }}
                </button>
                <button type="button" id="{{ $id }}_fullscreen_btn" class="btn btn-xs btn-outline-secondary" title="{{ trans('lavero-admin-ckeditor::ckeditor.fullscreen') }}" data-label-enter="{{ trans('lavero-admin-ckeditor::ckeditor.fullscreen') }}" data-label-exit="{{ trans('lavero-admin-ckeditor::ckeditor.exit_fullscreen') }}">
                    <i class="icon-expand me-1"></i> <span>{{ trans('lavero-admin-ckeditor::ckeditor.fullscreen') }}</span>
                </button>
            </div>
            <textarea name="{{$name}}" rows="{{ $rows }}" id="{{ $id }}" placeholder="{{ $placeholder }}" {!! $attributes !!}>{{ old($column, $value) }}</textarea>
        </div>

@include("admin::form._footer")
