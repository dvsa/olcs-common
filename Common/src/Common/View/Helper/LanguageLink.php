<?php

/**
 * Language Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;

use Laminas\View\Helper\AbstractHelper;
use Common\Preference\Language;

/**
 * Language Link
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LanguageLink extends AbstractHelper
{
    private Language $languagePref;

    public function __construct(Language $languagePref)
    {
        $this->languagePref = $languagePref;
    }

    public function __invoke()
    {
        if ($this->languagePref->getPreference() === Language::OPTION_CY) {
            return '<a class="govuk-footer__link" href="?lang=en">English</a>';
        }
        return '<a class="govuk-footer__link" href="?lang=cy">Cymraeg</a>';
    }
}
