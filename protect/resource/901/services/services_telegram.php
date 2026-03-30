<?php 

$service_telegram = 

[
    'id' => TELEGRAM_ID,
    'name' => 'TELEGRAM',
    'theme' => '#5682a3',
    'content' => mcrTextOrMedia,
    'rules' => [
        'text' => [
            'required' => mprOr,
            'max_size' => 2000,
        ],
        'file' => [
            'required' => mprOr,
            'max_size' => 1024,
        ],
        "address" => [
            "label" => "Phone",
            "regex" => "\d{7,15}",
        ],
    ],
    'methods' => [
        /*
        ** driver: BROWSER
        */
        [
            'id' => METHOD_WEB_ID,
            'name' => METHOD_WEB_NAME,
            'type' => 'mtBROWSER',
            'program' => [
                'file' => 'web.exe',
                'show' => SW_SHOWMINNOACTIVE,
            ],
            'providers' => [
                [
                    'id' => TELEGRAM_ID,
                    'name' => 'TELEGRAM',
                    'label' => 'TELEGRAM',
                    'uuid' => uidDetectable,
                    'rotational' => FALSE,
                    'patterns' => [
                        'interval1'        => 3,
                        'interval2'        => 5,
                        'small_limit'      => 10,
                        'small_interval'   => 60,
                        'daily_limit'      => 300,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'url1' => 'https://web.telegram.org/',
                        'res1' => [
                            'resource:jquery',
                            //'jq_1',
                            'common_1',
                            'status_1',
                            'jobs_1',
                            'telegram_1',
                        ],
                    ],
                ]
            ]
        ],
        /*
        ** driver: API
        
        [
            'id' => METHOD_API_ID,
            'name' => 'API',
            'type' => 'mtPROGRAM',
            'program' => [
                'file' => 'api.exe',
                'show' => SW_HIDE,
            ],
            'providers' => [
                /*
                ** WHATSAPP
                
                [
                    'id' => TELEGRAM_ID,
                    'name' => 'TELEGRAM',
                    'uuid' => uidNone,
                    'logable' => FALSE,
                ],
                
                [
                    'id' => TWILIO_ID,
                    'name' => 'TWILIO',
                    'uuid' => uidRegistrable,
                    'logable' => TRUE,
                ],
            ]
        ]
        */
    ]
];