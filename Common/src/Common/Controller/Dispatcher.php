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

    public function __construct(object $delegate)
    {
        $this->delegate = $delegate;
    }

    public function getDelegate(): object
    {
        return $this->delegate;
    }

    /**
     * Calls an action.
     *
     * @return mixed
     */
    public function callAction()
    {
        $event = $this->getEvent();
        $request = $event->getRequest();
        $routeMatch = $event->getRouteMatch();
        $response = $event->getResponse();

        $action = $routeMatch->getParam('action');
        if (empty($action)) {
            return $this->newControllerActionNotFoundResponse($event);
        }

        $actionMethod = sprintf('%sAction', $action);
        if (! is_callable([$this->delegate, $actionMethod])) {
            return $this->newControllerActionNotFoundResponse($event);
        }

        return $this->delegate->$actionMethod($request, $routeMatch, $response);
    }

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
