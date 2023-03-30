<?php

namespace Billing;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

const VERSION = '/v1';

return [
    'router' => [
        'routes' => [
            'billing-index' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => VERSION . '/billing-home',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'index',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
            'billing-post' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => VERSION . '/billing-post',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'post',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
            'billing' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => VERSION . '/billing[/:id]',
                    'defaults' => [
                        'controller'    => Controller\BillingController::class,
                        'isAuthorizationRequired' => true,
                        'methodsAuthorization'    => ['GET', 'POST', 'PUT', 'DELETE'],
                    ],
                    'constraints' => [
                        'formatter' => '[a-zA-Z0-9_-]*',
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
