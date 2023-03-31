<?php

namespace Billing;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

const VERSION_V1 = '/v1';

return [
    'router' => [
        'routes' => [
            'billing-index' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => VERSION_V1 . '/billing/send-file',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'sendFile',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
            'billing-webhook' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => VERSION_V1 . '/billing/webhook',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'webhook',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AttributeDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ],
    ],
];
