<?php

namespace Common\Service\Cqrs;

use Zend\Http\Header\Authorization;
use Zend\Http\Header\Cookie;
use Zend\Http\Headers;
use Zend\Http\Header\Accept;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Anon Request Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AnonRequestFactory extends RequestFactory
{
    /**
     * Build the auth header without using authorization service
     *
     * @param $serviceLocator
     * @return Authorization
     */
    protected function getAuthorizationHeader($serviceLocator)
    {
        // @to-do CHANGE THIS BEFORE GO LIVE

        $auth = new Authorization(1);
        return $auth;
    }
}
