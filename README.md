# CKEditor for LaveroAdmin

Integrates CKEditor 4 into LaveroAdmin form fields.

Repository: [https://github.com/LaveroAdmin/ckeditor](https://github.com/LaveroAdmin/ckeditor)

## Installation

```bash
composer require laveroadmin/ckeditor

php artisan vendor:publish --tag=lavero-admin-ckeditor
```

## Configuration

In `config/admin.php`, add configuration under the `extensions` section. You can specify any CKEditor options either directly or inside a `config` array:

```php
'extensions' => [
    'ckeditor' => [
        // Enable or disable extension
        'enable' => true,

        // Optional custom JS path
        // 'js_path' => 'vendor/laveroadmin/ckeditor/ckeditor.js',

        // Optional extra scripts and styles for plugins
        // 'js' => ['path/to/custom-plugin.js'],
        // 'css' => ['path/to/custom.css'],

        // Toggle media browser integration (default true)
        // 'media_browse' => true,

        // CKEditor options can be set directly at top level:
        'skin' => 'moono-lisa',
        'height' => 400,
        'language' => 'ru',

        // Or grouped under 'config' => [...]:
        'config' => [
            'height' => 400,
            'toolbar' => [
                ['name' => 'document', 'items' => ['Source', '-', 'Preview']],
                ['name' => 'clipboard', 'items' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']],
                ['name' => 'basicstyles', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']],
                ['name' => 'paragraph', 'items' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']],
                ['name' => 'links', 'items' => ['Link', 'Unlink']],
                ['name' => 'insert', 'items' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar']],
                '/',
                ['name' => 'styles', 'items' => ['Styles', 'Format', 'Font', 'FontSize']],
                ['name' => 'colors', 'items' => ['TextColor', 'BGColor']],
                ['name' => 'tools', 'items' => ['Maximize']],
            ],
            'filebrowserUploadUrl' => '/admin/upload',
            'removeButtons' => '',
            'extraPlugins' => '',
        ],
    ]
]
```

## Usage

Use the `ckeditor` field in form definitions:

```php
$form->ckeditor('content');

// Fluent configuration methods
$form->ckeditor('content')
    ->height(500)
    ->language('ru')
    ->skin('moono-lisa')
    ->toolbar('full');

// Pass custom options or array of config
$form->ckeditor('content')->config(['height' => 600, 'extraPlugins' => 'codesnippet']);
$form->ckeditor('content')->options(['height' => 600]);

// Disable media-manager image picker if needed
$form->ckeditor('content')->disableMediaBrowse();
```

## License

Licensed under [The MIT License (MIT)](LICENSE).

## Thanks

- Special thanks to [open-admin](https://github.com/open-admin-org/open-admin) for open-admin development.
- Special thanks to [z-song](https://github.com/z-song) for original development.
