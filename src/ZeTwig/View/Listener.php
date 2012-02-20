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

use ArrayAccess,
    Zend\Di\Locator,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\EventManager\StaticEventCollection,
    Zend\Http\PhpEnvironment\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\View\Renderer as ZendViewRenderer;
/**
 * ZeTwig View Listener class
 * @package ZeTwig
 * @author Cosmin Harangus <cosmin@zendexperts.com>
 */
class Listener implements ListenerAggregate
{
    /**
     * @var string
     */
    protected $layout;
    /**
     * @var array
     */
    protected $listeners = array();
    /**
     * @var array
     */
    protected $staticListeners = array();
    /**
     * @var \Zend\View\Renderer
     */
    protected $view;
    /**
     * @var bool
     */
    protected $displayExceptions = false;


    /**
     * @param \Zend\View\Renderer $renderer
     * @param string $layout
     */
    public function __construct(ZendViewRenderer $renderer, $layout = 'layout')
    {
        $this->view   = $renderer;
        $this->layout = $layout;
    }

    /**
     * @param $flag
     * @return Listener
     */
    public function setDisplayExceptionsFlag($flag)
    {
        $this->displayExceptions = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * @param \Zend\EventManager\EventCollection $events
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch.error', array($this, 'renderError'));
        $this->listeners[] = $events->attach('dispatch', array($this, 'render404'), -1000);
        $this->listeners[] = $events->attach('dispatch', array($this, 'renderLayout'), -80);
    }

    /**
     * @param \Zend\EventManager\EventCollection $events
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $key => $listener) {
            $events->detach($listener);
            unset($this->listeners[$key]);
            unset($listener);
        }
    }

    /**
     * @param \Zend\EventManager\StaticEventCollection $events
     * @param $locator
     */
    public function registerStaticListeners(StaticEventCollection $events, $locator)
    {
        $ident   = 'Zend\Mvc\Controller\ActionController';
        $handler = $events->attach($ident, 'dispatch', array($this, 'renderView'), -50);
        $this->staticListeners[] = array($ident, $handler);
    }

    /**
     * @param \Zend\EventManager\StaticEventCollection $events
     */
    public function detachStaticListeners(StaticEventCollection $events)
    {
        foreach ($this->staticListeners as $i => $info) {
            list($id, $handler) = $info;
            $events->detach($id, $handler);
            unset($this->staticListeners[$i]);
        }
    }

    /**
     * Render the view
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @return string
     */
    public function renderView(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response->isSuccess()) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller', 'index');
        $action     = $routeMatch->getParam('action', 'index');
        $script     = $controller . '/' . $action;

        $vars       = $e->getResult();
        if (is_scalar($vars)) {
            $vars = array('content' => $vars);
        } elseif (is_object($vars)) {
            $vars = (array) $vars;
        }

        $content    = $this->view->render($script , $vars);

        $e->setParam('content', $content);
        return $content;
    }

    /**
     * Render the layout
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @return mixed|\Zend\Http\PhpEnvironment\Response
     */
    public function renderLayout(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }
        if ($response->isRedirect()) {
            return $response;
        }

        $vars = $e->getResult();
        if (is_scalar($vars)) {
            $vars = array('content' => $vars);
        } elseif (is_object($vars)) {
            $vars = (array) $vars;
        }

        if (false !== ($contentParam = $e->getParam('content', false))) {
            $vars['content'] = $contentParam;
        }

        $layout   = $this->view->render($this->layout, $vars);
        $response->setContent($layout);
        return $response;
    }

    /**
     * Render 404 Error page
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @return mixed|\Zend\Http\PhpEnvironment\Response
     */
    public function render404(MvcEvent $e)
    {
        $vars = $e->getResult();
        if ($vars instanceof Response) {
            return;
        }

        $response = $e->getResponse();
        if ($response->getStatusCode() != 404) {
            // Only handle 404's
            return;
        }

        $vars = array(
            'message'            => 'Page not found.',
            'exception'          => $e->getParam('exception'),
            'display_exceptions' => $this->displayExceptions(),
        );

        $content = $this->view->render('error/404', $vars);

        $e->setResult($content);

        return $this->renderLayout($e);
    }

    /**
     * Render error page
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @return mixed|\Zend\Http\PhpEnvironment\Response
     */
    public function renderError(MvcEvent $e)
    {
        $error    = $e->getError();
        $app      = $e->getTarget();
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
                $vars = array(
                    'message'            => 'Page not found.',
                    'exception'          => $e->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                );
                $response->setStatusCode(404);
                break;

            case Application::ERROR_EXCEPTION:
            default:
                $exception = $e->getParam('exception');
                $vars = array(
                    'message'            => 'An error occurred during execution; please try again later.',
                    'exception'          => $e->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                );
                $response->setStatusCode(500);
                break;
        }

        $content = $this->view->render('error/index', $vars);

        $e->setResult($content);

        return $this->renderLayout($e);
    }
}
