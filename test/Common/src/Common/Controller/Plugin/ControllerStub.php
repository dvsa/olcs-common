<?php

namespace CommonTest\Common\Controller\Plugin;

use Laminas\Mvc\Controller\AbstractActionController as LaminasAbstractActionController;
use Laminas\View\Helper\Placeholder;

/**
 * Class TestController
 * Provuides a controlled and consistent environment with which to test the plugin.
 *
 * @package OlcsTest\Controller\Plugin
 */
class ControllerStub extends LaminasAbstractActionController
{
    protected Placeholder $placeholder;

    public function __construct(Placeholder $placeholder)
    {
        $this->placeholder = $placeholder;
    }

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Method to test the invoking of the plugin with array of options
     * @param $options
     * @return mixed
     */
    public function pluginInvoke($options)
    {
        return $this->ElasticSearch($options);
    }

    /**
     * Method to return the plugin
     * @return mixed
     */
    public function getPlugin()
    {
        return $this->ElasticSearch();
    }

    /**
     * Method called by controller as a result of plugin calls. Not tested here.
     *
     * @param string|ViewModel $view
     * @param null $pageTitle
     * @param null $pageSubTitle
     * @return string|ViewModel
     */
    public function renderView($view, $pageTitle = null, $pageSubTitle = null)
    {
        $view->pageTitle = $pageTitle;
        $view->pageSubTitle = $pageSubTitle;

        return $view;
    }
}
