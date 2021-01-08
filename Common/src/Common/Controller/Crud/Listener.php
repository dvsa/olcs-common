<?php

/**
 * Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Listener implements EventManagerAwareInterface, ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use EventManagerAwareTrait;
    use ListenerAggregateTrait;
    use ServiceLocatorAwareTrait;

    protected $controller;

    // @NOTE 2/3/17 - below default config doesn't seem to work, would be very useful
    protected $defaultCrudConfig = [
        'add' => [
            'class' => 'action--primary',
            'label' => 'action_links.add',
            'requireRows' => false
        ],
        'edit' => [
            'label' => 'action_links.edit',
            'requireRows' => true
        ],
        'delete' => [
            'label' => 'action_links.remove',
            'requireRows' => true
        ]
    ];

    /**
     * Pass the controller in
     *
     * @param \Laminas\Mvc\Controller\AbstractActionController $controller
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

    /**
     * Check for crud actions before hitting the controller action
     *
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        // If we are not posting we can return early
        $request = $this->getServiceLocator()->get('Request');
        if (!$request->isPost()) {
            return;
        }

        $postData = (array)$request->getPost();

        if ($this->hasCancelled($postData)) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addInfoMessage('flash-discarded-changes');
            return $this->setResult($e, $this->controller->redirect()->toRouteAjax(null));
        }

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
            return $this->setResult($e, $this->controller->redirect()->refresh());
        }

        $params = ['action' => $requestedAction];

        if ($actionConfig['requireRows']) {
            $params['id'] = $ids;
        }

        return $this->setResult($e, $this->controller->redirect()->toRoute(null, $params, [], true));
    }

    /**
     * Check if the user has cancelled the action
     *
     * @param array $postData
     * @return boolean
     */
    protected function hasCancelled($postData)
    {
        return isset($postData['form-actions']['cancel']);
    }

    /**
     * Get the crud config or use the default
     *
     * @param string $routeName
     * @return array
     */
    protected function getCrudConfig($routeName)
    {
        $config = $this->getServiceLocator()->get('Config');

        if (isset($config['crud-config'][$routeName])) {
            return $config['crud-config'][$routeName];
        }

        return $this->defaultCrudConfig;
    }

    /**
     * Set the event result
     *
     * @param MvcEvent $e
     * @param mixed $result
     * @return mixed
     */
    protected function setResult($e, $result)
    {
        $e->setResult($result);

        return $result;
    }

    /**
     * Check if the post has a crud action
     *
     * @param array $postData
     * @return boolean
     */
    protected function hasCrudAction($postData)
    {
        return isset($postData['table']) && isset($postData['action']);
    }

    /**
     * Format the action from the crud action
     *
     * @param array $action
     * @return string
     */
    protected function formatAction($action)
    {
        if (is_array($action['action'])) {
            $action['action'] = key($action['action']);
        }

        return strtolower($action['action']);
    }

    /**
     * Format the id's from the crud action
     *
     * @param array $postData
     * @return string
     */
    protected function formatIds($postData)
    {
        if (is_array($postData['action'])) {
            $id = key(reset($postData['action']));
        } elseif (isset($postData['id'])) {
            $id = $postData['id'];
        } else {
            return null;
        }

        if (is_array($id)) {
            return implode(',', $id);
        }

        return $id;
    }
}
