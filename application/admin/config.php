<?php

return [
    'session'           => [
        'prefix'     => 'admin',
        'type'       => '',
        'auto_start' => true,
    ],

    // 加载独立配置
    'extra_config_list' => ['database', 'route', 'validate', 'tp5tool'],
];
