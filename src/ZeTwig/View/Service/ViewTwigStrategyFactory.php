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
    ZeTwig\View\Strategy\TwigRendererStrategy;

/**
 * ZeTwig service strategy factory
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class ViewTwigStrategyFactory implements FactoryInterface
{

    /**
     * Create and return the twig view strategy
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return TwigRendererStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $twigRenderer = $serviceLocator->get('ViewTwigRenderer');
        $twigStrategy = new TwigRendererStrategy($twigRenderer);
        return $twigStrategy;
    }

}