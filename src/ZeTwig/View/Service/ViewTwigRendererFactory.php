<?php
/**
 * This file is part of ZeTwig
 *
 * (c) 2012 ZendExperts <team@zendexperts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ZeTwig\View\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    ZeTwig\View\Strategy\TwigRendererStrategy,
    ZeTwig\View\Environment,
    ZeTwig\View\Renderer;

/**
 * ZeTwig service renderer factory
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class ViewTwigRendererFactory implements FactoryInterface
{

    /**
     * Create and return the twig view renderer
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return TwigRendererStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        $config = isset($config['zendexperts_zetwig']) && (is_array($config['zendexperts_zetwig']) || $config['zendexperts_zetwig'] instanceof ArrayAccess)
            ? $config['zendexperts_zetwig']
            : array();

        $viewLoader = $serviceLocator->get('view_manager')->getResolver();
        $loader = new \ZeTwig\View\Resolver();
        foreach($viewLoader->getIterator() as $resolver){
            if ($resolver instanceof \Zend\View\Resolver\TemplatePathStack){
                $resolver = clone $resolver;
                $resolver->setDefaultSuffix($config['template_suffix']);
            }
            $loader->attach($resolver);
        }

        $broker = $serviceLocator->get('view_manager')->getHelperBroker();
        $options = isset($config['environment_options']) ? $config['environment_options'] : array();
        $environment = new Environment($loader, $broker, $options);
        if (isset($config['extensions'])){
            foreach($config['extensions'] as $extension){
                $extensionInstance = new $extension();
                if ($extensionInstance instanceof \Twig_ExtensionInterface){
                    $environment->addExtension($extensionInstance);
                }
            }
        }

        $twigRenderer = new Renderer($environment);
        $this->defaultRendererSetup($twigRenderer);
        return $twigRenderer;
    }

    private function defaultRendererSetup($renderer)
    {
        $renderer->plugin('headTitle')
            ->setSeparator(' - ')
            ->setAutoEscape(false);
    }

}