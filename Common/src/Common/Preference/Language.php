<?php

/**
 * Language Preference
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Preference;

use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Header\SetCookie;
use Interop\Container\ContainerInterface;

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

    public function createService(ServiceLocatorInterface $serviceLocator): Language
    {
        return $this->__invoke($serviceLocator, Language::class);
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Language
    {
        $request = $container->get('Request');
        // if not an Http request (eg Console request) then don't do anything as
        // methods below only exists on Http Requests
        if (!$request instanceof Request) {
            return $this;
        }
        $cookie = $request->getCookie();
        $this->preference = self::OPTION_EN;
        $this->requestCookie = new SetCookie();
        if ($container->has('CookieCookieReader')) {
            $cookieState = $container->get('CookieCookieReader')->getState($cookie);

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
        $response = $container->get('Response');
        $response->getHeaders()->addHeader($this->requestCookie);
        return $this;
    }
}
