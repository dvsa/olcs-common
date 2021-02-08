<?php

namespace Common\Controller;

use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;

class Dispatcher extends AbstractOlcsController
{
    /**
     * @var object
     */
    private $delegate;

    /**
     * @param object $delegate
     */
    public function __construct(object $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @return object
     */
    public function getDelegate(): object
    {
        return $this->delegate;
    }

    /**
     * Calls an action.
     *
     * @return mixed
     */
    protected function callAction()
    {
        $event = $this->getEvent();
        $request = $event->getRequest();
        $routeMatch = $event->getRouteMatch();

        $action = $routeMatch->getParam('action');
        if (empty($action)) {
            return $this->newControllerActionNotFoundResponse($event);
        }

        $actionMethod = sprintf('%sAction', $action);
        if (! is_callable([$this->delegate, $actionMethod])) {
            return $this->newControllerActionNotFoundResponse($event);
        }

        return $this->delegate->$actionMethod($request, $routeMatch);
    }

    /**
     * @param MvcEvent $event
     * @return ViewModel
     */
    protected function newControllerActionNotFoundResponse(MvcEvent $event): ViewModel
    {
        $event->setError(Application::ERROR_CONTROLLER_CANNOT_DISPATCH);
        return $this->notFoundAction();
    }

    /**
     * @inheritDoc
     */
    public static function getMethodFromAction($action)
    {
        // Delegate all action calls to $this->callAction
        return 'callAction';
    }
}
