<?php

namespace Billing;

use Laminas\Router\Http\Literal;

return [
    'router' => [
        'routes' => [
            'billing-send-file' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/v1/billing/send-file',
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
                    'route'    => '/v1/billing/webhook',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'webhook',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
            'billing-consumer-files' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/v1/billing/consumer-files',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'consumerFiles',
                        'methodsAuthorization'    => ['GET'],
                    ],
                ],
            ],
            'billing-consumer-lines' => [ // @todo review routes to consumers
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/v1/billing/consumer-lines',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'consumerLines',
                        'methodsAuthorization'    => ['GET'],
                    ],
                ],
            ],
            'billing-consumer-emails' => [ // @todo review routes to consumers
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/v1/billing/consumer-emails',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'consumerEmails',
                        'methodsAuthorization'    => ['GET'],
                    ],
                ],
            ],
            'billing-consumer-payments' => [ // @todo review routes to consumers
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/v1/billing/consumer-payments',
                    'defaults' => [
                        'controller' => Controller\BillingController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'consumerPayments',
                        'methodsAuthorization'    => ['GET'],
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
