<?php

namespace LaveroAdmin\Admin\CKEditor;

use Illuminate\Support\ServiceProvider;
use LaveroAdmin\Admin\Admin;
use LaveroAdmin\Admin\Form;

class CKEditorServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(?CKEditor $extension = null)
    {
        $extension = $extension ?: new CKEditor();

        if (!CKEditor::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'lavero-admin-ckeditor');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/lavero-admin-ckeditor')],
                'lavero-admin-ckeditor'
            );
        }

        Admin::booting(function () {
            $jsPath = CKEditor::config('js_path') ?: 'vendor/lavero-admin-ckeditor/ckeditor.js';
            Admin::js($jsPath, false); // prevent minifying

            if ($extraJs = CKEditor::config('js')) {
                Admin::js((array) $extraJs);
            }

            if ($extraCss = CKEditor::config('css')) {
                Admin::css((array) $extraCss);
            }

            Form::extend('ckeditor', Editor::class);
        });
    }
}
