<?php
return array(
    'di' => array(
        'instance' => array(
            'alias'=>array(
                'view'=>'ZeTwig\View\Renderer'
            ),
            // Inject the plugin broker for controller plugins into
            // the action controller for use by all controllers that
            // extend it.
//            'Zend\Mvc\Controller\ActionController' => array(
//                'parameters' => array(
//                    'broker'       => 'Zend\Mvc\Controller\PluginBroker',
//                ),
//            ),
//            'Zend\Mvc\Controller\PluginBroker' => array(
//                'parameters' => array(
//                    'loader' => 'Zend\Mvc\Controller\PluginLoader',
//                ),
//            ),
//            'Zend\View\Resolver\TemplateMapResolver' => array(
//                'parameters' => array(
//                    'map'  => array(
//                    ),
//                ),
//            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'defaultSuffix'=>'twig',
                ),
            ),
            'ZeTwig\View\Resolver'=>array(
                'injections' => array(
                    'Zend\View\Resolver\TemplateMapResolver',
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),
            'ZeTwig\View\Renderer' => array(
                'parameters' => array(
                    'broker' => 'Zend\View\HelperBroker',
                    'environment'=>'ZeTwig\View\Environment',
                ),
            ),
            'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                'parameters' => array(
                    'layoutTemplate' => 'layouts/layout',
                ),
            ),
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'parameters' => array(
                    'displayExceptions' => true,
                    'exceptionTemplate' => 'error/index',
                ),
            ),
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'parameters' => array(
                    'displayNotFoundReason' => true,
                    'displayExceptions'     => true,
                    'notFoundTemplate'      => 'error/404',
                ),
            ),
            'ZeTwig\View\Environment'=>array(
                'injections' => array(
                    'ZeTwig\View\Extension'
                ),
                'parameters' => array(
                    'loader' => 'ZeTwig\View\Resolver',
                    'options' => array(
                        'cache' => BASE_PATH . '/data/cache/twig',
                        'auto_reload' => true,
                        'debug' => true
                    ),
                ),
            ),
        ),
    ),
);
