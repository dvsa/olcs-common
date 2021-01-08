<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Traits\ViewHelperManagerAware;
use Laminas\Mvc\Controller\AbstractActionController as LaminasAbstractActionController;

/**
 * Class TestController
 * Provuides a controlled and consistent environment with which to test the plugin.
 *
 * @package OlcsTest\Controller\Plugin
 */
class ControllerStub extends LaminasAbstractActionController
{
    use ViewHelperManagerAware;

    /**
     * Method to test the invoking of the plugin with array of options
     * @param $options
     * @return mixed
     */
    public function pluginInvoke($options)
    {
        $plugin = $this->ElasticSearch($options);

        return $plugin;
    }

    /**
     * Method to return the plugin
     * @return mixed
     */
    public function getPlugin()
    {
        $plugin = $this->ElasticSearch();

        return $plugin;
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
