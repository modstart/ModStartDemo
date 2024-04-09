<?php
return [

    'name' => 'ModStart',

    'trackMissingLang' => false,
    
    'trackPerformance' => env('TRACK_PERFORMANCE', false),
    
    'trackLongSqlThreshold' => env('TRACK_LONG_SQL_THRESHOLD', 5000),
    'statisticServer' => env('STATISTIC_SERVER', null),

    'forceSchema' => env('FORCE_SCHEMA', null),
    'subdirUrl' => env('SUBDIR_URL', null),
    'subdir' => env('SUBDIR', '/'),

        'xForwardedHostVisitRedirect' => true,

    'admin' => [
        'disabled' => env('ADMIN_DISABLED', false),
        
        'prefix' => trim(env('ADMIN_PATH', 'admin'), '/'),
        'directory' => app_path('Admin'),
        'login' => [
            
            'captcha' => true,
        ],
        'versionCheckDisable' => env('ADMIN_VERSION_CHECK_DISABLE', false),
        'upgradeDisable' => env('ADMIN_UPGRADE_DISABLE', false),
        'theme' => env('ADMIN_THEME', 'light'),
        'tabsEnable' => env('ADMIN_TABS_ENABLE', true),
        
        'authIgnores' => [],
        
        'i18n' => [
            
            'enable' => false,
            'langs' => [
                'zh' => '简体中文',
                'en' => 'English',
            ]
        ]
    ],

    'web' => [
        'prefix' => trim(env('APP_PATH', ''), '/'),
        'directory' => app_path('Web'),
    ],

    'api' => [
        'prefix' => trim(env('API_PATH', 'api'), '/'),
        'directory' => app_path('Api'),
    ],

    'openApi' => [
        'prefix' => trim(env('API_PATH', 'open_api'), '/'),
        'directory' => app_path('OpenApi'),
    ],

    'asset' => [
        'driver' => \ModStart\Core\Assets\Driver\LocalAssetsPath::class,
        'cdn' => env('CDN_URL', '/'),
        'image_none' => '',
    ],

    'config' => [
        'driver' => \ModStart\Core\Config\Driver\DatabaseMConfig::class,
    ],

    'i18n' => [
        'langs' => [
            'zh' => '简体中文',
            'en' => 'English',
        ]
    ]

];
