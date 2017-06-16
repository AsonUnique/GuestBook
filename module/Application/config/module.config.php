<?php

namespace Application;

use Application\Controller\AdminController;
use Application\Controller\IndexController;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default'             => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'home'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin'       => [
                'type'          => Literal::class,
                'options'       => [
                    'route'    => '/admin',
                    'defaults' => [
                        'controller' => AdminController::class,
                        'action'     => 'admin',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'sign-in' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/sign-in',
                            'defaults' => [
                                'action' => 'signIn',
                            ],
                        ],
                    ],
                    'post' => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'      => '/post/:id',
                            'constraint' => [
                                'id' => '[0-9]*',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'public' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/public',
                                    'defaults' => [
                                        'action' => 'public',
                                    ],
                                ],
                            ],
                            'hide'   => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/hide',
                                    'defaults' => [
                                        'action' => 'hide',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/delete',
                                    'defaults' => [
                                        'action' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'partial/pagination'      => __DIR__ . '/../view/partial/pagination.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
        'strategies'               => [
            'ViewJsonStrategy',
        ],
    ],
];
