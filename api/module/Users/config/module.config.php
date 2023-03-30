<?php

namespace Users;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'token' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/token',
                    'defaults' => [
                        'controller' => Controller\TokenController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'index',
                        'methodsAuthorization'    => ['POST'],
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:id]',
                    'defaults' => [
                        'controller'    => Controller\UsersController::class,
                        'isAuthorizationRequired' => true,
                        'methodsAuthorization'    => ['GET', 'POST', 'PUT', 'DELETE'],
                    ],
                    'constraints' => [
                        'formatter' => '[a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'user-account' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/user-account',
                    'defaults' => [
                        'controller' => Controller\AccountController::class,
                        'isAuthorizationRequired' => true,
                        'action'     => 'index',
                        'methodsAuthorization'    => ['GET'],
                    ],
                ],
                'may_terminate' => true,

                'child_routes' => [
                    'recover-password' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/recover-password',
                            'defaults' => [
                                'controller'    => Controller\AccountController::class,
                                'isAuthorizationRequired' => false,
                                'action'     => 'recoverPassword',
                                'methodsAuthorization'    => ['POST'],
                            ],
                            'constraints' => [
                                'formatter' => '[a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/logout',
                            'defaults' => [
                                'controller'    => Controller\AccountController::class,
                                'isAuthorizationRequired' => true,
                                'action'     => 'logout',
                                'methodsAuthorization'    => ['GET'],
                            ],
                            'constraints' => [
                                'formatter' => '[a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                    'recover-consumer' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/recover-consumer',
                            'defaults' => [
                                'controller'    => Controller\AccountController::class,
                                'isAuthorizationRequired' => false,
                                'action'     => 'recoverConsumer',
                                'methodsAuthorization'    => ['GET'],
                            ],
                            'constraints' => [
                                'formatter' => '[a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
            'users-password-db' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/users/password-test',
                    'defaults' => [
                        'controller'    => Controller\UsersController::class,
                        'isAuthorizationRequired' => false,
                        'action' => 'passwordDb',
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
