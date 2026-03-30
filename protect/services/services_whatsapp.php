<?php 

$service_whatsapp =

[
    'id' => WHATSAPP_ID,
    'name' => 'WHATSAPP',
    'theme' => '#71B340',// '#009688',
    'content' => mcrTextAndMedia,
    'optOut'  => true,
    'rules' => [
        'text' => [
            'required' => mprAndOr,
            'max_size' => 2000,
        ],
        'file' => [
            'required' => mprAndOr,
            'max_size' => 1024,
            'filter'   => 'Arquivos suportados|*.png;*.jpg;*.jpeg;*.pdf;*.wav;*.mp3',

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
                    'id' => WHATSAPP_ID,
                    'name' => 'WHATSAPP',
                    'label' => 'WHATSAPP',
                    'uuid' => uidDetectable,
                    'rotational' => FALSE,
                    'patterns' => [
                        'interval1'        => 50,
                        'interval2'        => 60,
                        'small_limit'      => 10,
                        'small_interval'   => 60,
                        'daily_limit'      => 200,
                        'max_attemps'      => 1,
                        'max_targets_fail' => 0,
                    ],
                    'parameters' => [
                        'url1' => 'https://web.whatsapp.com/',
                        
                        'res1' => [
                            'resource:jquery',
                            //'common_1',
                            'status_1',
                            '00_boot_1',
                        ],
                        
                        'res2' => [
                            'wapp_1'        ,
                            '10_wapp_1'     ,
                            '20_device_1'   ,
                            '30_profile_1'  ,
                            //'40_picture_1'  ,
                            '50_batch_1'    ,
                            '60_messages_1' ,
                            '65_groups_1'   ,
                            '70_contacts_1' ,
                            '80_status_1'   ,

                            /*
                            'jq_1'                ,
                            'common_1'            ,
                            'status_1'            ,
                            'whatsapp_status_1'  
                            */

                            '90_start_1'   
                        ],
                    ]
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
                /
                [
                    'id' => WHATSAPP_ID,
                    'name' => 'WHATSAPP',
                    'uuid' => uidNone,
                    'logable' => FALSE,
                ],
                [
                    'id' => TWILIO_ID,
                    'name' => 'TWILIO',
                    'uuid' => uidRegistrable,
                    'logable' => FALSE,
                ],
            ]
        ]
        */
    ]
];