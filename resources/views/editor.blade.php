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
        </style>
        <div id="{{ $id }}_wrap" class="lavero-ckeditor-wrap">
            <div class="lavero-ckeditor-actions">
                <button type="button" id="{{ $id }}_media_btn" class="btn btn-xs btn-outline-secondary" title="Media Manager">
                    <i class="icon-image me-1"></i> Media
                </button>
                <button type="button" id="{{ $id }}_fullscreen_btn" class="btn btn-xs btn-outline-secondary" title="Fullscreen">
                    <i class="icon-expand me-1"></i> Fullscreen
                </button>
            </div>
            <textarea name="{{$name}}" rows="{{ $rows }}" id="{{ $id }}" placeholder="{{ $placeholder }}" {!! $attributes !!}>{{ old($column, $value) }}</textarea>
        </div>

@include("admin::form._footer")
