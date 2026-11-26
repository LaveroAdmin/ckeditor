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

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lavero-admin-ckeditor');

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/lavero-admin-ckeditor')],
                'lavero-admin-ckeditor'
            );
        }

        Admin::booting(function () {
            $version = (int) CKEditor::config('version', 5);
            $defaultJs = $version === 4
                ? 'vendor/lavero-admin-ckeditor/ckeditor.js'
                : 'https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js';
            $jsPath = CKEditor::config('js_path') ?: $defaultJs;
            Admin::js($jsPath, false); // prevent minifying

            if ($version !== 4 && !CKEditor::config('js_path')) {
                $locale = strtolower(str_replace('_', '-', app()->getLocale()));
                $langCode = explode('-', $locale)[0];
                if ($langCode && $langCode !== 'en') {
                    Admin::js("https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/translations/{$langCode}.js", false);
                }
            }

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
