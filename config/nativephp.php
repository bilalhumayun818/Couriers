<?php

return [
    /**
     * The version of your app.
     */
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),

    /**
     * The ID of your application. Must be a unique reverse-domain identifier.
     */
    'app_id' => env('NATIVEPHP_APP_ID', 'com.cxcourier.app'),

    /**
     * Application Name displayed in Windows Taskbar & System Tray
     */
    'name' => env('NATIVEPHP_APP_NAME', 'CX COURIER'),

    /**
     * Deep linking scheme.
     */
    'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME', 'cxcourier'),

    /**
     * The author of your application.
     */
    'author' => env('NATIVEPHP_APP_AUTHOR', 'CX Courier'),

    /**
     * The copyright notice.
     */
    'copyright' => env('NATIVEPHP_APP_COPYRIGHT', 'Copyright © ' . date('Y') . ' CX Courier'),

    /**
     * The description of your application.
     */
    'description' => env('NATIVEPHP_APP_DESCRIPTION', 'CX Courier Management System'),

    /**
     * The Website of your application.
     */
    'website' => env('NATIVEPHP_APP_WEBSITE', 'https://cxcourier.com'),

    /**
     * Service provider for bootstrapping windows, hotkeys, menus.
     */
    'provider' => \App\Providers\NativeAppServiceProvider::class,

    'cleanup_env_keys' => [
        'AWS_*',
        'AZURE_*',
        'GITHUB_*',
        'DO_SPACES_*',
        '*_SECRET',
        'BIFROST_*',
        'NATIVEPHP_UPDATER_PATH',
    ],

    'cleanup_exclude_files' => [
        'build',
        'temp',
        'content',
        'node_modules',
        '*/tests',
    ],

    /**
     * Disable auto-updater unless you have an active S3/DigitalOcean Space set up.
     */

'updater' => [
        'enabled' => env('NATIVEPHP_UPDATER_ENABLED', false),
        'default' => env('NATIVEPHP_UPDATER_PROVIDER', 's3'),
        'providers' => [
            's3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'bucket' => env('AWS_BUCKET', 'my-bucket'),
                'endpoint' => env('AWS_ENDPOINT'),
                'path' => env('NATIVEPHP_UPDATER_PATH', null),
            ],
        ],
    ],

    'queue_workers' => [
        'default' => [
            'queues' => ['default'],
            'memory_limit' => 128,
            'timeout' => 60,
            'sleep' => 3,
        ],
    ],

    'prebuild' => [],

    'postbuild' => [],

    'nsis' => [
        'delete_app_data_on_uninstall' => env('NATIVEPHP_NSIS_DELETE_APP_DATA', false),
    ],

    'binary_path' => env('NATIVEPHP_PHP_BINARY_PATH', null),
];