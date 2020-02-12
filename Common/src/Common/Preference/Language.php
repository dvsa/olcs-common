<?php

/**
 * Language Preference
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Preference;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;

/**
 * Language Preference
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Language implements FactoryInterface
{
    const OPTION_EN = 'en';

    const OPTION_CY = 'cy';

    private $options = [
        self::OPTION_EN => 'English',
        self::OPTION_CY => 'Cymraeg'
    ];

    /**
     * @var SetCookie
     */
    private $requestCookie;

    private $preference;

    private $key = 'langPref';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $request = $serviceLocator->get('Request');
        // if not an Http request (eg Console request) then don't do anything as
        // methods below only exists on Http Requests
        if (!$request instanceof Request) {
            return $this;
        }

        $cookie = $request->getCookie();

        $this->preference = self::OPTION_EN;
        $this->requestCookie = new SetCookie();

        if ($serviceLocator->has('CookieCookieReader')) {
            $cookieState = $serviceLocator->get('CookieCookieReader')->getState($cookie);

            if (!$cookieState->isActive('settings')) {
                return $this;
            }
        }

        if ($cookie instanceof Cookie && isset($cookie[$this->key])) {
            $this->preference = $cookie[$this->key];
        }

        $this->requestCookie->setName($this->key);
        $this->requestCookie->setValue($this->preference);
        $this->requestCookie->setPath('/');
        $this->requestCookie->setExpires(strtotime('+10 years'));

        /** @var Response $response */
        $response = $serviceLocator->get('Response');
        $response->getHeaders()->addHeader($this->requestCookie);

        return $this;
    }

    public function setPreference($preference)
    {
        if (!array_key_exists($preference, $this->options)) {
            throw new \Exception('Invalid language preference option');
        }

        $this->preference = $preference;

        $this->requestCookie->setValue($this->preference);
    }

    public function getPreference()
    {
        return $this->preference;
    }
}
