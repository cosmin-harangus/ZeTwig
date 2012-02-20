<?php
/**
 * This file is part of ZeTwig
 *
 * (c) 2012 ZendExperts <team@zendexperts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ZeTwig\View;

use Zend\View\Renderer as ViewRenderer,
    Zend\Loader\Pluggable,
    Zend\Filter\FilterChain,
    ZeTwig\View\Environment;

/**
 * ZeTwig Renderer
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class Renderer implements ViewRenderer, Pluggable
{
    /**
     * @var null|\ZeTwig\View\Environment
     */
    private $_environment = null;
    /**
     * @var null
     */
    private $_filterChain = null;

    /**
     * @param \ZeTwig\View\Environment $environment
     * @param array $config Configuration options
     */
    public function __construct(Environment $environment, $config = array())
    {
        $this->_environment = $environment;
    }

    public function setEnvironmentOptions($options)
    {
        $this->_environment->setEnvironmentOptions($options);
    }

    /**
     * Processes a view template and returns the output.
     *
     * @param string $name The template name to process.
     * @param array $context The variables with which to render the template
     * @return string The script output.
     */
    public function render($name, $context = array())
    {
        $output = $this->_environment->render($name,$context);
        return $this->getFilterChain()->filter($output);
    }


    #GETTERS AND SETTERS


    /**
     * Return the template engine object, if any
     *
     * @return \ZeTwig\View\Renderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Get plugin broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker()
    {
        $this->_environment->getBroker();
    }

    /**
     * Set plugin broker instance
     *
     * @param  string|Broker $broker Plugin broker to load plugins
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker)
    {
        $this->_environment->setBroker($broker);
        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name  Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->_environment->plugin($name, $options);
    }

    /**
     * Set filter chain
     *
     * @param \Zend\Filter\FilterChain $filters
     * @return Renderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->_filterChain = $filters;
        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     *
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->_filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->_filterChain;
    }
}
