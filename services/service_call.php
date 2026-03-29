<?php 

$service_call =

[
    'id' => CALL_ID,
    'name' => 'CALL',
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
        ** driver: WEB
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
    ]
];