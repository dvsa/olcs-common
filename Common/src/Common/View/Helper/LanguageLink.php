<?php

/**
 * Language Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;
use Common\Preference\Language;

/**
 * Language Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LanguageLink extends AbstractHelper implements FactoryInterface
{
    /**
     * @var Language
     */
    private $languagePref;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->languagePref = $serviceLocator->getServiceLocator()->get('LanguagePreference');

        return $this;
    }

    public function __invoke()
    {
        if ($this->languagePref->getPreference() === Language::OPTION_CY) {
            return '<a class="govuk-footer__link" href="?lang=en">English</a>';
        } else {
            return '<a class="govuk-footer__link" href="?lang=cy">Cymraeg</a>';
        }
    }
}
