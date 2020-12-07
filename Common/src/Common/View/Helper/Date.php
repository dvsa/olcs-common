<?php

/**
 * Date
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\I18n\View\Helper\Translate;

/**
 * Date
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Date extends AbstractHelper implements FactoryInterface
{
    /**
     * @var Translate
     */
    protected $translator;

    /**
     * Inject the translator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->translator = $serviceLocator->get('translate');

        return $this;
    }

    /**
     * Output the date in a specific format, or alternative if null
     *
     * @param int|null $timestamp
     * @param string $dateFormat
     * @param string $altIfNull
     * @return string
     */
    public function __invoke($timestamp = null, $dateFormat = \DATE_FORMAT, $altIfNull = 'Unknown')
    {
        if (empty($timestamp)) {
            $translate = $this->translator;
            return $translate($altIfNull);
        }

        return date($dateFormat, $timestamp);
    }
}
