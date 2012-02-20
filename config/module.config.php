<?php
return array(
    'ze_twig' => array(
        'extension' => '.twig',
        'alias'=>array(
            '@tpl.root'             => 'layouts/main',
            '@tpl.root_one_column'  => 'layouts/main_one_column',
            '@tpl.root_two_columns' => 'layouts/main_two_columns',
        ),
    ),
    'display_exceptions'    => true,
    'di'                    => array(
        'instance' => array(
            'alias' => array(
                'view'  => 'ZeTwig\View\Renderer',
            ),
            'ZeTwig\View\Renderer' => array(
                'parameters' => array(
                    'environment'=>'ZeTwig\View\Environment',
                ),
            ),
            'ZeTwig\View\Environment'=>array(
                'parameters' => array(
                    'loader' => 'ZeTwig\View\Loader',
                    'broker' => 'Zend\View\HelperBroker',
                    'options' => array(
                        'cache' => BASE_PATH . '/data/cache/twig',
                        'auto_reload' => true,
                        'debug' => true
                    ),
                ),
            ),
            'ZeTwig\View\Loader'=>array(
                'parameters' => array(
                    'paths'=> array()
                )
            ),
        ),
    ),
);
