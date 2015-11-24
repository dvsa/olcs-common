<?php

namespace Common\Service\Cqrs;

use Dvsa\Olcs\Utils\Auth\AuthHelper;
use Zend\Http\Header\Authorization;
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

        if (AuthHelper::isOpenAm()) {
            $userRequest = $serviceLocator->get('Request');
            if ($userRequest instanceof Request) {
                $cookies = $userRequest->getCookie();
                if (isset($cookies['secureToken'])) {
                    $secureToken = new Cookie(['secureToken' => $cookies['secureToken']]);
                    $headers->addHeader($secureToken);
                }
            }
        } else {
            $headers->addHeader($this->getAuthorizationHeader($serviceLocator));
        }

        $request = new Request();
        $request->setHeaders($headers);

        return $request;
    }

    /**
     * @TODO Remove this logic when we are 100% using openAm
     */
    protected function getAuthorizationHeader($serviceLocator)
    {
        /** @var \ZfcRbac\Service\AuthorizationService $userProvider */
        $auth = $serviceLocator->get('ZfcRbac\Service\AuthorizationService');
        $identity = $auth->getIdentity();
        //$userId = $identity->getId();

        // Temporary commit to get around problem of allowing anonymous user access.
        // @to-do CHANGE THIS BEFORE GO LIVE
        // CRiley Authorised this commit. Piotr provided the code. For CraigR to use.

        $userId = ($identity instanceOf \ZfcRbac\Identity\IdentityInterface) ? $identity->getId() : 1;

        $auth = new Authorization($userId);
        return $auth;
    }
}
