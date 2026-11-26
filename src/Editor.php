<?php

namespace LaveroAdmin\Admin\CKEditor;

use Illuminate\Support\Arr;
use LaveroAdmin\Admin\Form\Field\Textarea;

class Editor extends Textarea
{
    protected $view = 'lavero-admin-ckeditor::editor';

    /**
     * Whether media browsing is enabled for this instance.
     *
     * @var bool|null
     */
    protected $mediaBrowse = null;

    /**
     * Enable media browser integration.
     *
     * @return $this
     */
    public function enableMediaBrowse()
    {
        $this->mediaBrowse = true;

        return $this;
    }

    /**
     * Disable media browser integration.
     *
     * @return $this
     */
    public function disableMediaBrowse()
    {
        $this->mediaBrowse = false;

        return $this;
    }

    /**
     * Set CKEditor configuration option(s).
     *
     * @param array|string $key
     * @param mixed|null   $val
     *
     * @return $this
     */
    public function config($key, $val = null)
    {
        if (is_array($key)) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $val;
        }

        return $this;
    }

    /**
     * Set editor height.
     *
     * @param int|string $height
     *
     * @return $this
     */
    public function height($height)
    {
        return $this->config('height', $height);
    }

    /**
     * Set editor toolbar.
     *
     * @param array|string $toolbar
     *
     * @return $this
     */
    public function toolbar($toolbar)
    {
        return $this->config('toolbar', $toolbar);
    }

    /**
     * Set editor language.
     *
     * @param string $language
     *
     * @return $this
     */
    public function language($language)
    {
        return $this->config('language', $language);
    }

    /**
     * Set editor skin.
     *
     * @param string $skin
     *
     * @return $this
     */
    public function skin($skin)
    {
        return $this->config('skin', $skin);
    }

    /**
     * Setup default image browse URLs if not explicitly specified.
     *
     * @param array $globalConfig
     *
     * @return void
     */
    public function setupImageBrowse(array $globalConfig = [])
    {
        $mediaUrl = admin_url('media');
        $selectFn = 'window.opener.' . $this->id . '_selectFile';

        if (!isset($this->options['filebrowserBrowseUrl']) && !isset($globalConfig['filebrowserBrowseUrl'])) {
            $this->options['filebrowserBrowseUrl'] = $mediaUrl . '?select=true&close=true&fn=' . $selectFn;
        }

        if (!isset($this->options['filebrowserImageBrowseUrl']) && !isset($globalConfig['filebrowserImageBrowseUrl'])) {
            $this->options['filebrowserImageBrowseUrl'] = $mediaUrl . '?select=true&close=true&fn=' . $selectFn;
        }
    }

    /**
     * Render the CKEditor field.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        $extConfig = (array) CKEditor::config();

        // 1. Extract nested 'config' array if provided
        $nestedConfig = isset($extConfig['config']) && is_array($extConfig['config'])
            ? $extConfig['config']
            : [];

        // 2. Extract top-level options, excluding extension-specific infrastructure keys
        $reservedKeys = ['enable', 'config', 'js_path', 'js', 'css', 'media_browse'];
        $topLevelConfig = Arr::except($extConfig, $reservedKeys);

        // 3. Global config merges top-level and nested 'config'
        $globalConfig = array_merge($topLevelConfig, $nestedConfig);

        // 4. Determine media browse state (field option > admin.php config > default true)
        $mediaBrowseEnabled = $this->mediaBrowse;
        if ($mediaBrowseEnabled === null) {
            $mediaBrowseEnabled = (bool) ($extConfig['media_browse'] ?? true);
        }

        if ($mediaBrowseEnabled) {
            $this->setupImageBrowse($globalConfig);
        }

        // 5. Final config merges global config with field-level options (field options take precedence)
        $mergedConfig = array_merge($globalConfig, $this->options);
        $configJson = json_encode($mergedConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $version = (int) CKEditor::config('version', 5);
        $mediaUrl = admin_url('media') . '?select=true&close=true&fn=window.opener.' . $this->id . '_selectFile';
        $langCode = explode('-', strtolower(str_replace('_', '-', app()->getLocale())))[0] ?: 'en';

        if ($version === 4) {
            $this->script = <<<JS
function {$this->id}_selectFile(url, file_name) {
    if (typeof CKEDITOR === 'undefined') return;
    var dialog = CKEDITOR.dialog.getCurrent();
    if (!dialog) return;
    var dialogName = dialog.getName();

    if (dialogName === 'link') {
        var urlElem = dialog.getContentElement('info', 'url');
        if (urlElem) urlElem.setValue(url);
        var linkDisplayText = dialog.getContentElement('info', 'linkDisplayText');
        if (linkDisplayText && linkDisplayText.getValue() === "") {
            linkDisplayText.setValue(file_name);
        }
    } else {
        dialog.selectPage('info');
        var txtUrl = dialog.getContentElement('info', 'txtUrl');
        if (txtUrl) txtUrl.setValue(url);
        var txtAlt = dialog.getContentElement('info', 'txtAlt');
        if (txtAlt) txtAlt.setValue(file_name);
    }
}
window.{$this->id}_selectFile = {$this->id}_selectFile;

CKEDITOR.replace('{$this->id}', {$configJson});

if (typeof window.CKupdate === 'undefined') {
    window.CKupdate = function() {
        for (var instance in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(instance)) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
    };
    if (window.admin && window.admin.form) {
        admin.form.addSaveCallback(window.CKupdate);
    }
}
JS;
        } else {
            $this->script = <<<JS
(function() {
    window.laveroCk5Instances = window.laveroCk5Instances || {};
    var el = document.getElementById('{$this->id}');
    var wrapEl = document.getElementById('{$this->id}_wrap');
    var mediaBtn = document.getElementById('{$this->id}_media_btn');
    var fsBtn = document.getElementById('{$this->id}_fullscreen_btn');
    var EditorClass = (window.CKEDITOR && window.CKEDITOR.ClassicEditor) ? window.CKEDITOR.ClassicEditor : window.ClassicEditor;
    if (!el || !EditorClass) return;

    var cfg = {$configJson};
    delete cfg.filebrowserBrowseUrl;
    delete cfg.filebrowserImageBrowseUrl;

    if (window.CKEDITOR && window.CKEDITOR.ClassicEditor && EditorClass === window.CKEDITOR.ClassicEditor) {
        if (!cfg.language) {
            cfg.language = '{$langCode}';
        }
        if (!cfg.ui) {
            cfg.ui = { viewportOffset: { top: 58 } };
        }
        if (!cfg.toolbar) {
            cfg.toolbar = {
                items: [
                    'undo', 'redo', '|',
                    'findAndReplace', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'code', 'removeFormat', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'subscript', 'superscript', '|',
                    'alignment', '|',
                    'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                    'link', 'mediaEmbed', 'insertTable', 'blockQuote', 'codeBlock', 'horizontalLine', 'specialCharacters', '|',
                    'htmlEmbed', 'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            };
        }
        if (!cfg.removePlugins) {
            cfg.removePlugins = [
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments',
                'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
                'WProofreader', 'MathType', 'SlashCommand', 'Template',
                'DocumentOutline', 'FormatPainter', 'TableOfContents',
                'PasteFromOfficeEnhanced', 'CaseChange', 'MultiLevelList',
                'ExportPdf', 'ExportWord', 'ImportWord'
            ];
        }
        if (!cfg.image) {
            cfg.image = {
                insert: {
                    integrations: ['url']
                }
            };
        }
        if (!cfg.htmlSupport) {
            cfg.htmlSupport = { allow: [{ name: /.*/, attributes: true, classes: true, styles: true }] };
        }
    }

    EditorClass.create(el, cfg).then(function(editor) {
        window.laveroCk5Instances['{$this->id}'] = editor;
        editor.model.document.on('change:data', function() {
            el.value = editor.getData();
        });
    }).catch(function(err) {
        console.error('CKEditor 5 init error:', err);
    });

    if (mediaBtn) {
        mediaBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.open('{$mediaUrl}', 'MediaManager', 'width=960,height=640');
        });
    }

    if (fsBtn && wrapEl) {
        fsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isFs = wrapEl.classList.toggle('lavero-ckeditor-fullscreen');
            fsBtn.classList.toggle('btn-primary', isFs);
            fsBtn.classList.toggle('btn-outline-secondary', !isFs);
            var span = fsBtn.querySelector('span');
            if (span) {
                span.textContent = isFs ? (fsBtn.dataset.labelExit || 'Exit') : (fsBtn.dataset.labelEnter || 'Fullscreen');
            }
        });
    }

    window.{$this->id}_selectFile = function(url, file_name) {
        var editor = window.laveroCk5Instances['{$this->id}'];
        if (!editor) return;
        editor.model.change(function(writer) {
            var isImg = /\.(jpg|jpeg|png|gif|webp|svg)(\?.*)?$/i.test(url);
            if (isImg) {
                var imageElement = writer.createElement('imageBlock', { src: url, alt: file_name || '' });
                editor.model.insertContent(imageElement, editor.model.document.selection);
            } else {
                var text = writer.createText(file_name || url, { linkHref: url });
                editor.model.insertContent(text, editor.model.document.selection);
            }
        });
    };

    if (!window._laveroCk5Bound) {
        window._laveroCk5Bound = true;
        if (window.admin && window.admin.form) {
            admin.form.addSaveCallback(function() {
                Object.keys(window.laveroCk5Instances || {}).forEach(function(id) {
                    var target = document.getElementById(id);
                    if (target && window.laveroCk5Instances[id]) {
                        target.value = window.laveroCk5Instances[id].getData();
                    }
                });
            });
        }
    }
})();
JS;
        }

        return parent::render();
    }
}

