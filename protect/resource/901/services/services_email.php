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
            'max_size' => 512,
        ],
        'file' => [
            'required' => mprAndOr,
            'max_size' => 512,
        ],
        "address" => [
            "label" => "Email",
            "regex" => "(?<name>[\w.]+)\@(?<domain>\w+\.\w+)(\.\w+)?",
        ],
        "subject" => [
            "max_length" => 255,
        ],
    ],
    'methods' => [
        /*
        ** driver: SMTP
        */
        [
            'id' => METHOD_SMTP_ID,
            'name' => 'SMTP',
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
                    'uuid' => uidNone,
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
                    'uuid' => uidNone,
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
                            'host=smtp.mail.yahoo.com',
                            'port=465',
                            "Use SSL=YES"
                        ],
                        'fields' => [
                            'Email=',
                            'Password=',
                        ],
                    ]
                ),
                /*  
                ** provider: CUSTOMIZED
                */
                array(
                    'id' => PROVIDER_SMTP_ID,
                    'name' => 'CUSTOMIZED',
                    'label' => 'API/SMTP',
                    'uuid' => uidNone,
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