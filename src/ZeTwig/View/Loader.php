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

use Zend\View\TemplatePathStack,
    Twig_LoaderInterface as LoaderInterface;

/**
 * ZeTwig Loader class
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class Loader extends TemplatePathStack implements LoaderInterface
{
    /**
     * @var null|array
     */
    protected $_config = null;

    /**
     * Gets the source code of a template, given its name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The template source code
     */
    public function getSource($name)
    {
        $path = $this->getScriptPath($name);
        return file_get_contents($path);
    }

    /**
     * Setter for config
     * @param array $config
     * @return Loader
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Getter for config
     * @return null | array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        $path = $this->getScriptPath($name);
        return $path;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     * @return boolean
     */
    public function isFresh($name, $time)
    {
        $path = $this->getScriptPath($name);
        return filemtime($path) < $time;
    }

    /**
     * @param $name
     * @return string
     */
    public function getScriptPath($name)
    {

        if (isset($this->_config) && isset($this->_config['alias']) && !empty($this->_config['alias'])){
            if (array_key_exists($name, $this->_config['alias'])){
                $name = $this->_config['alias'][$name];
            }
        }
        $name .= $this->_config['extension'];
        return parent::getScriptPath($name);
    }


}