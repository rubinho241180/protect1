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
            'filter'   => 'Arquivos suportados|*.png;*.jpg;*.jpeg;*.pdf;*.wav;*.mp3',

            /*ADDED*/

            'types'    => [
                
                [
                    'type'    => 'IMAGE',
                    'filter'  => '*.png;*.jpg;*.jpeg',
                    'required' => mprOr,
                ],
                [
                    'type'    => 'AUDIO',
                    'filter'  => '*.mp3',
                    'required' => mprOr,
                ],
                [
                    'type'    => 'FILE',
                    'filter'  => '*.pdf;*.doc;*.xls;*.xlsx',
                    'required' => mprOr,
                ],

            ],
            
            /******/

        ],
        "address" => [
            "label" => "Phone",
            "treat" => "[^\d]",
            "regex" => "\d{7,15}",
            "prefix" => "{system.country.ddi}",
        ],
    ],
    'methods' => [
        /*
        ** driver: BROWSER
        */
        [
            'id' => METHOD_WEB_ID,
            'name' => METHOD_WEB_NAME,
            'label' => METHOD_WEB_LABEL,
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
                    'authType' => 'atManual',
                    'patterns' => [
                        'interval1'        => 15,
                        'interval2'        => 30,
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