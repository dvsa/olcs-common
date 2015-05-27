<?php

namespace Common\Service\Cqrs;

use Zend\Http\Header\Authorization;
use Zend\Http\Headers;
use Zend\Http\Header\Accept;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RequestFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $accept = new Accept();
        $accept->addMediaType('application/json');

        $contentType = new ContentType();
        $contentType->setMediaType('application/json');

        $headers = new Headers();
        $headers->addHeaders([$accept, $contentType, $this->getAuthorizationHeader($serviceLocator)]);

        $request = new Request();
        $request->setHeaders($headers);

        return $request;
    }

    /**
     * @TODO replace this logic with actually implementation of auth and correct method of sending token to this is only
     * temporary
     */
    protected function getAuthorizationHeader($serviceLocator)
    {
        /** @var \ZfcRbac\Service\AuthorizationService $userProvider */
        $auth = $serviceLocator->get('ZfcRbac\Service\AuthorizationService');
        $identity = $auth->getIdentity();
        $userId = $identity->getId();

        $auth = new Authorization($userId);
        return $auth;
    }
}
