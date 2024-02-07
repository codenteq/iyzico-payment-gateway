<?php

return [
    [
        'key'    => 'sales.payment_methods.iyzico',
        'name'   => 'iyzico::app.iyzico.name',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'iyzico::app.iyzico.system.title',
                'type'          => 'text',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'iyzico::app.iyzico.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'iyzico::app.iyzico.system.image',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'iyzico::app.iyzico.system.status',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
