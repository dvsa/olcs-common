<?php

/**
 * Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Listener implements EventManagerAwareInterface, ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use EventManagerAwareTrait,
        ListenerAggregateTrait,
        ServiceLocatorAwareTrait;

    protected $controller;

    protected $defaultCrudConfig = [
        'add' => ['requireRows' => false],
        'edit' => ['requireRows' => true],
        'delete' => ['requireRows' => true]
    ];

    /**
     * Pass the controller in
     *
     * @param \Zend\Mvc\Controller\AbstractActionController $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 20);
    }

    public function onDispatch(MvcEvent $e)
    {
        // If we are not posting we can return early
        $request = $this->getServiceLocator()->get('Request');
        if (!$request->isPost()) {
            return;
        }

        $postData = (array)$request->getPost();

        // If we don't have a table and action
        if (!$this->hasCrudAction($postData)) {
            return;
        }

        $routeName = $e->getRouteMatch()->getMatchedRouteName();

        // Grab the crud config from the controller
        $crudConfig = $this->getCrudConfig($routeName);

        $requestedAction = $this->formatAction($postData);

        // @NOTE If we are not expecting the action then bail
        if (!isset($crudConfig[$requestedAction])) {
            return;
        }

        $actionConfig = $crudConfig[$requestedAction];
        $ids = $this->formatIds($postData);

        if ($actionConfig['requireRows'] && $ids === null) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage('please-select-row');
            return $this->setResult($e, $this->controller->redirect()->refreshAjax());
        }

        $params = ['action' => $requestedAction];

        if ($ids !== null) {
            $params['id'] = $ids;
        }

        return $this->setResult($e, $this->controller->redirect()->toRouteAjax(null, $params, [], true));
    }

    protected function getCrudConfig($routeName)
    {
        $config = $this->getServiceLocator()->get('Config');

        if (isset($config['crud-config'][$routeName])) {
            return $config['crud-config'][$routeName];
        }

        return $this->defaultCrudConfig;
    }

    protected function setResult($e, $result)
    {
        $e->setResult($result);

        return $result;
    }

    /**
     * @NOTE This method will need extending
     *
     * @param array $postData
     * @return boolean
     */
    protected function hasCrudAction($postData)
    {
        return isset($postData['table']) && isset($postData['action']);
    }

    /**
     * @NOTE This method will need extending
     *
     * @param array $action
     * @return string
     */
    protected function formatAction($action)
    {
        return strtolower($action['action']);
    }

    /**
     * @NOTE This method will need extending
     *
     * @param array $postData
     * @return string
     */
    protected function formatIds($postData)
    {
        if (!isset($postData['id'])) {
            return null;
        }

        if (is_array($postData['id'])) {
            return implode(',', $postData['id']);
        }

        return $postData['id'];
    }
}
