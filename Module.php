<?php

/**
 * This file is part of ZeTwig
 *
 * (c) 2012 ZendExperts <team@zendexperts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ZeTwig;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\ModuleEvent;

/**
 * ZeTwig Module class
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class Module implements AutoloaderProvider
{
    /**
     * @var \ZeTwig\View\Renderer
     */
    protected $view;
    /**
     * @var \ZeTwig\View\Listener
     */
    protected $viewListener;

    /**
     * @var null | array
     */
    private $_options = null;

    /**
     * Module initialization
     * @param \Zend\Module\Manager $moduleManager
     */
    public function init(Manager $moduleManager)
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 100);
        $moduleManager->events()->attach('loadModules.post', array($this, 'postInit'));
    }

    /**
     * Load full configuration options
     * @param \Zend\Module\ModuleEvent $e
     * @return void
     */
    public function postInit(ModuleEvent $e)
    {
        $config = $e->getConfigListener()->getMergedConfig();
        $config = $config->toArray();
        $this->_options = $config['ze_twig'];
    }

    /**
     * Get Autoloader Config
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload/classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
                'prefixes' => array(
                    'Twig' => __DIR__ . '/vendor/Twig/lib/Twig'
                )
            ),
        );
    }

    /**
     * Get Module Configuration
     * @return mixed
     */
    public function getConfig()
    {
        $definitions = include __DIR__ . '/config/module.di.config.php';
        $config = include __DIR__ . '/config/module.config.php';
        $config = array_merge_recursive($definitions, $config);
        return $config;
    }

    /**
     * Handle View Initialization
     * @param $e
     */
    public function initializeView($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('config');
        $view         = $this->getView($app);
        $viewListener = $this->getViewListener($view, $config);
        $app->events()->attachAggregate($viewListener);
        $events       = StaticEventManager::getInstance();
        $viewListener->registerStaticListeners($events, $locator);
    }

    /**
     * Load the view listener
     * @param $view
     * @param $config
     * @return View\Listener
     */
    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }

        $viewListener = new View\Listener($view, $config->layout);
        $viewListener->setDisplayExceptionsFlag($config->display_exceptions);

        $this->viewListener = $viewListener;
        return $viewListener;
    }

    /**
     * Load the view with configured options
     * @param $app
     * @return mixed
     */
    protected function getView($app)
    {
        if ($this->view) {
            return $this->view;
        }

        $di     = $app->getLocator();
        $view   = $di->get('view');
        $view->setEnvironmentOptions($this->_options);
        $basePath = $app->getRequest()->getBasePath();
        $view->plugin('basePath')->setBasePath($basePath);
        $view->plugin('url')->setRouter($app->getRouter());
        $view->plugin('headTitle')->setSeparator(' - ')
                                  ->setAutoEscape(false)
                                  ->append('ProjectQuery');

        $this->view = $view;
        return $view;
    }
}
