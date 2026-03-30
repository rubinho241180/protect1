<?php 

$service_sms =

[
    'id' => SMS_ID,
    'name' => 'SMS',
    'theme' => '#2d3f51',
    'content' => mcrText,
    'rules' => [
        'text' => [
            'required' => mprAnd,
            'max_size' => 1024,
        ],
        'file' => [
            'required' => mprUnsupported,
            'max_size' => 1024,
        ],
        "address" => [
            "label" => "Phone",
            //"treat" => "((\+)|(\s)|(\-)|(\()|(\)))",
            "treat" => "[^\d]",
            "regex" => "\d{7,15}",
            "prefix" => "{system.country.ddi}",
        ],
    ],
    'methods' => [
        /*
        ** driver: GSM
        */
        [
            'id' => METHOD_GSM_ID,
            'name' => 'USB',
            'label' => METHOD_GSM_LABEL,
            'type' => 'mtPROGRAM',
            'program' => [
                'file' => 'gsm.exe',
                'show' => SW_HIDE,
            ],
            'providers' => [
                //TIM, VIVO, NEXTEL
            ],
            'providersType' => ptDynamic,
        ],

        /*
        ** driver: API
        */
        [
            'id' => METHOD_API_ID,
            'name' => 'API',
            'label' => METHOD_API_LABEL,
            'type' => 'mtPROGRAM',
            'program' => [
                'file' => 'api.exe',
                'show' => SW_HIDE,
            ],
            'providers' => [
                /*
                ** OWNER
                
                [
                    'id' => PROVIDER_TRUE_ID,
                    'name' => 'MrSENDER',
                    'label' => 'SHORTCODE',
                    'uuid' => uidRegistrable,
                    'rotational' => FALSE,
                    'balance' => TRUE,
                    'patterns' => [
                        'interval1'        => 1,
                        'interval2'        => 2,
                        'small_limit'      => 0,
                        'small_interval'   => 0,
                        'daily_limit'      => 0,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'driver=API',
                            'method=GET',
                            'url=http://3.16.156.61:9091/apisms/v1/tsm/sendsms/{api_token}/{text}/{to}',
                            'fields=',
                            'credentials=api_token={api_token}'
                        ],
                        'balance' => [
                            'method=GET',
                            'url=http://3.16.156.61:9091/apisms/v1/tsm/consulta_saldo/{api_token}',
                        ],
                        'fields' => [
                            'api_token=',
                        ],
                    ],
                    'hidden' => TRUE,
                ],
                */
                

                /*
                ** TWILIO
                */
                [
                    'id' => TWILIO_ID,
                    'name' => 'TWILIO',
                    'label' => 'TWILIO',
                    'uuid' => uidRegistrable,
                    'rotational' => FALSE,
                    'balance' => FALSE,
                    'patterns' => [
                        'interval1'        => 1,
                        'interval2'        => 2,
                        'small_limit'      => 0,
                        'small_interval'   => 0,
                        'daily_limit'      => 0,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'driver=API',
                            'method=GET',
                            //'url=http://3.16.156.61:9091/apisms/v1/tsm/sendsms/{api_token}/{text}/{to}',
                            'url=https://mex10.com/api/shortcodev2.aspx?token={api_token}&t=send&n={to}&m={text}',
                            'fields=',
                            'credentials=api_token={api_token}'
                        ],
                        'balance' => [
                            'method=GET',
                            'url=http://3.16.156.61:9091/apisms/v1/tsm/consulta_saldo/{api_token}',
                        ],
                        'fields' => [
                            'api_token=',
                        ],
                    ],
                    'hidden' => TRUE,
                ],


                /*
                ** ZENVIA
                */
                [
                    'id' => ZENVIA_ID,
                    'name' => 'ZENVIA',
                    'label' => 'ZENVIA',
                    'uuid' => uidRegistrable,
                    //'threadType' => ttChannel,
                    //'rotational' => FALSE,
                    //'balance' => FALSE,
                    'channel' => [
                        'label' => 'Zenvia Account Email',

                    ],
                    'patterns' => [
                        'interval1'        => 1,
                        'interval2'        => 2,
                        'small_limit'      => 0,
                        'small_interval'   => 0,
                        'daily_limit'      => 0,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'driver=API',
                            'method=GET',
                            'url=http://www.mocky.io/v2/5e8b81c32f0000670088c363',
                            //'fields=',
                            //'credentials=api_token={api_token}'
                        ],
                        'balance' => [
                            'method=GET',
                            'url=http://3.16.156.61:9091/apisms/v1/tsm/consulta_saldo/{api_token}',
                        ],
                        'fields' => [
                            'api_token=',
                        ],
                    ],
                    'hidden' => TRUE,
                ],


            ]
        ],


        /*
        ** driver: WEB
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
                    'uuid' => uidRegistrable,
                    'rotational' => FALSE,
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
                            'jq_1'              ,
                            'common_1'          ,
                            'status_1'          ,
                            'jobs_1'            ,
                            'textnow_1'         ,
                            'textnow_status_1'  ,
                            'textnow_banners_1' ,
                        ],
                    ],
                    'hidden' => TRUE,
                ]
                
            ]
        ],        

        /*
        ** driver: GOIP
        
        [
            'id' => METHOD_GOIP_ID,
            'name' => 'GoIP',
            'label' => METHOD_GOIP_LABEL,
            'type' => 'mtPROGRAM',
            'program' => [
                'file' => 'api.exe',
                'show' => SW_HIDE,
            ],
            'providers' => [
                [
                    'id' => PROVIDER_GOIP_ID,
                    'name' => 'DBL',
                    'label' => 'GoIP (DBL)',
                    'uuid' => uidRegistrable,
                    //'threadType' => ttSession,
                    'rotational' => TRUE,
                    'patterns' => [
                        'interval1'        => 5,
                        'interval2'        => 10,
                        'small_limit'      => 10,
                        'small_interval'   => 60,
                        'daily_limit'      => 100,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'driver=GoIP',
                            'method=GET',
                            //'url=http://{host}/default/en_US/send.html',
                            'url=http://www.mocky.io/v2/5e8b81c32f0000670088c363',
                            //'fields=l={line}&n={to}&m={text}',
                            //'credentials=u={user}&p={password}'
                        ],
                        'fields' => [
                            'host=',
                            'line=',
                            'user=',
                            'password='
                        ],
                        
                        // 'credentials' => array2str([
                        //     'u={user}',
                        //     'p={password}'
                        // ]),
                        
                    ]
                ],
            ]
        ],
        */
        



        /*
        ** driver: BROWSER
        
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
                    'id' => MESSAGES_ID,
                    'name' => 'MESSAGES',
                    'label' => 'ANDROID MESSAGES',
                    'uuid' => uidNone,
                    'logable' => FALSE,
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
                        'url1' => 'http://messages.android.com/',
                        'res1' => array2str([
                            'jq_1'        ,
                            'common_1'     ,
                            'status_1'   ,
                            'jobs_1'  ,
                            'messages_1'  ,
                            'messages_status_1'  ,
                        ]),
                    ]
                ]
            ]
        ],
        */
        
        
    ]
];