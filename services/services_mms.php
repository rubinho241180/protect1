<?php 

$service_mms = 

[
    'id' => MMS_ID,
    'name' => 'MMS',
    'theme' => '#3079ea',
    'content' => mcrTextAndMedia,
    'rules' => [
        'text' => [
            'required' => mprAndOr,
            'max_size' => 512,
        ],
        'file' => [
            'required' => mprAnd,
            'max_size' => 512,
            'filter'   => 'Arquivos suportados|*.png;*.jpg;*.jpeg',

            /*ADDED*/

            'types'    => [
                
                [
                    'type'    => 'IMAGE',
                    'filter'  => '*.png;*.jpg;*.jpeg',
                    'required' => mprAndOr,
                ],
                [
                    'type'    => 'AUDIO',
                    'filter'  => '*.mp3',
                    'required' => mprAndOr,
                ],
                [
                    'type'    => 'FILE',
                    'filter'  => '*.pdf;*.doc;*.xls;*.xlsx',
                    'required' => mprAndOr,
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
                    'id' => MESSAGES_ID,
                    'name' => 'MESSAGES',
                    'label' => 'ANDROID MESSAGES',
                    'uuid' => uidNone,
                    //'uuid' => uidRegistrable,
                    'rotational' => FALSE,
                    'patterns' => [
                        'interval1'        => 10,
                        'interval2'        => 15,
                        'small_limit'      => 10,
                        'small_interval'   => 60,
                        'daily_limit'      => 600,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'url1' => 'http://messages.android.com/',
                        'res1' => [
                            'jq_1'               ,
                            'common_1'           ,
                            'status_1'           ,
                            'jobs_1'             ,
                            'messages_1'         ,
                            'messages_status_1'  ,
                        ],
                    ]
                ],
                
                [
                    'id' => TEXTNOW_ID,
                    'name' => 'TEXTNOW',
                    'label' => 'TEXTNOW (USA/CA)',
                    'uuid' => uidNone,
                    'rotational' => TRUE,
                    'patterns' => [
                        'interval1'        => 1,
                        'interval2'        => 5,
                        'small_limit'      => 10,
                        'small_interval'   => 60,
                        'daily_limit'      => 600,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'url1' => 'https://www.textnow.com/messaging',
                        'res0' => [
                            'textnow_readyToInject_1'
                        ],
                        'res2' => [
                            'jq_1'                ,
                            'common_1'            ,
                            'status_1'            ,
                            'jobs_1'              ,
                            'textnow_1'           ,
                            'textnow_status_7_1'  ,
                            'textnow_banners_1'   ,
                            'textnow_login_1'     ,
                        ],
                        'setup' => [
                            'email=Email',
                            'password=Password',
                        ],
                        'fields' => [
                            'email=',
                            'password=',
                        ],
                    ],
                    'hidden' => TRUE,
                ]
                
            ]
        ],
        /*
        ** driver: API
        
        [
            'id' => METHOD_API_ID,
            'name' => 'API',
            'type' => 'mtAPI',
            'providers' => [
                /*
                ** WHATSAPP
                
                [
                    'id' => TWILIO_ID,
                    'name' => 'TWILIO',
                    'uuid' => uidNone,
                    'logable' => FALSE,
                ],

            ]
        ]
        */
    ]
];