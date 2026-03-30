<?php 

$service_email = 

[
    'id' => EMAIL_ID,
    'name' => 'EMAIL',
    'theme' => "#725ad2",//"#ed2637",
    'content' => mcrTextAndMedia,
    'rules' => [
        'text' => [
            'required' => mprAndOr,
            'max_size' => 10000,
        ],
        'file' => [
            'required' => mprAndOr,
            'max_size' => 512,
            'filter'   => 'Arquivos suportados|*.png;*.jpg;*.jpeg;*.pdf',

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
            "label" => "Email",
            "treat" => "",
            //"regex" => "(?<name>[\w.]+)\@(?<domain>\w+\.\w+)(\.\w+)?",
            "regex" => "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]*[a-zA-Z0-9]+$",
        ],
        "subject" => [
            "max_length" => 255,
        ],
    ],
    'methods' => [
        /*
        ** driver: WEB
        *
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
                /*
                ** gmail
                *
                [
                    'id' => PROVIDER_GMAIL_ID,
                    'name' => 'GMAIL',
                    'label' => 'GMAIL',
                    'uuid' => uidRegistrable,
                    'authType' => 'atCredential',
                    'rotational' => TRUE,
                    'patterns' => [
                        'interval1'        => 5,
                        'interval2'        => 10,
                        'small_limit'      => 5,
                        'small_interval'   => 60,
                        'daily_limit'      => 100,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'url1' => 'https://accounts.google.com/signin/v2/identifier?flowName=GlifWebSignIn&flowEntry=ServiceLogin',
                        'res1' => [
                            'resource:jquery',
                            'common_1',
                            'status_1',
                            'jobs_1',
                            'gmail_1'         ,
                            'gmail_status_1'  ,
                        ],
                        // 'setup' => [
                        //     'host=smtp.gmail.com',
                        //     'port=587',
                        //     "Use SSL=YES"
                        // ],
                        'fields' => [
                            'Email=',
                            'Password=',
                        ],
                    ],
                    'hidden' => TRUE,
                ],
                
            ]
        ],

        /*
        ** driver: SMTP
        */
        [
            'id' => METHOD_SMTP_ID,
            'name' => 'SMTP',
            'label' => METHOD_SMTP_LABEL,
            'type' => 'mtPROGRAM',
            'program' => [
                'file' => 'smtp.exe',
                'show' => SW_HIDE,
            ],
            'providers' => [
                /*  
                ** provider: GMAIL
                */
                array(
                    'id' => PROVIDER_GMAIL_ID,
                    'name' => 'GMAIL',
                    'label' => 'GMAIL',
                    'uuid' => uidRegistrable,
                    'rotational' => TRUE,
                    'balance' => FALSE,
                    
                    "THREAD" => [
                        "TYPE" => "ttStatic", //ttDynamicChannel, ttDynamicSession
                        "dynamic" => "tdChannel", //tdSession
                    ],

                    'patterns' => [
                        'interval1'        => 30,
                        'interval2'        => 45,
                        'small_limit'      => 5,
                        'small_interval'   => 60,
                        'daily_limit'      => 100,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'host=smtp.gmail.com',
                            'port=587',
                            "Use SSL=YES"
                        ],
                        'fields' => [
                            'Email=',
                            'Password=',
                        ],
                    ]
                ),
                /*  
                ** provider: YAHOO
                */
                array(
                    'id' => PROVIDER_YAHOO_ID,
                    'name' => 'YAHOO',
                    'label' => 'YAHOO',
                    'uuid' => uidRegistrable,
                    'rotational' => TRUE,
                    'balance' => FALSE,
                    'patterns' => [
                        'interval1'        => 5,
                        'interval2'        => 10,
                        'small_limit'      => 5,
                        'small_interval'   => 60,
                        'daily_limit'      => 100,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            'host=smtp.mail.yahoo.com',
                            'port=465',
                            "Use SSL=YES"
                        ],
                        'fields' => [
                            'Email=',
                            'Password=',
                        ],
                    ],
                    'hidden' => TRUE,
                ),
                /*  
                ** provider: CUSTOMIZED
                */
                array(
                    'id' => PROVIDER_SMTP_ID,
                    'name' => 'CUSTOMIZED',
                    'label' => 'OUTROS',
                    'uuid' => uidRegistrable,
                    'rotational' => FALSE,
                    'balance' => FALSE,
                    'patterns' => [
                        'interval1'        => 5,
                        'interval2'        => 10,
                        'small_limit'      => 5,
                        'small_interval'   => 60,
                        'daily_limit'      => 100,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'setup' => [
                            //
                        ],
                        'fields' => [
                            'Email=',
                            'Password=',
                            'Host=',
                            'Port=',
                            //"secure=NONE|SSL/TLS"
                            "Use SSL=NO|YES"
                        ],
                    ]
                ),
            ]
        ],

    ]
];