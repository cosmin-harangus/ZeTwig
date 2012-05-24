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

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\Mvc\MvcEvent;

/**
 * ZeTwig Module class
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class Module implements AutoloaderProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    public function onBootstrap(MvcEvent $event)
    {
        // Set the static service manager instance so we can use it everywhere in the module
        $app = $event->getApplication();
        static::$serviceManager = $app->getServiceManager();
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
            ),
        );
    }

    /**
     * Get Service Configuration
     * @return array
     */
    public function getServiceConfiguration(){
        return include __DIR__ . '/config/service.config.php';
    }

    /**
     * Get Module Configuration
     * @return mixed
     */
    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        return $config;
    }

    /**
     * Return the ServiceManager instance
     * @static
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

}