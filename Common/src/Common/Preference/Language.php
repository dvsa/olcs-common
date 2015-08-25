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
        $cookie = $serviceLocator->get('Request')->getCookie();

        $this->preference = self::OPTION_EN;

        if ($cookie instanceof Cookie && isset($cookie[$this->key])) {
            $this->preference = $cookie[$this->key];
        }

        $this->requestCookie = new SetCookie();
        $this->requestCookie->setName($this->key);
        $this->requestCookie->setValue($this->preference);

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
