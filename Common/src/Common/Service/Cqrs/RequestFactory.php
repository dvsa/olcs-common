<?php

namespace Common\Service\Cqrs;

use Zend\Http\Header\Cookie;
use Zend\Http\Headers;
use Zend\Http\Header\Accept;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Request Factory
 */
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
        $headers->addHeaders([$accept, $contentType]);

        $userRequest = $serviceLocator->get('Request');
        if ($userRequest instanceof Request) {
            $cookies = $userRequest->getCookie();
            if (isset($cookies['secureToken'])) {
                $secureToken = new Cookie(['secureToken' => $cookies['secureToken']]);
                $headers->addHeader($secureToken);
            }
        }

        $request = new Request();
        $request->setHeaders($headers);

        return $request;
    }
}
