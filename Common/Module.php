<?php

/**
 * ZF2 Module
 */

namespace Common;

use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\I18n\Translator\Translator;

/**
 * ZF2 Module
 */
class Module
{

    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');

        $translator->setLocale($this->getLanguageLocalePreference())
            ->setFallbackLocale('en_GB');

        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/language/', '%s.php');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/sic-codes/', 'sicCodes_%s.php');

        $events = $e->getApplication()->getEventManager();

        $missingTranslationProcessor = new \Common\Service\Translator\MissingTranslationProcessor(
            // Inject the renderer and template resolver
            $e->getApplication()->getServiceManager()->get('ViewRenderer'),
            $e->getApplication()->getServiceManager()->get('Zend\View\Resolver\TemplatePathStack')
        );

        $events->attach(
            Translator::EVENT_MISSING_TRANSLATION,
            array($missingTranslationProcessor, 'processEvent')
        );

        $translator->enableEventManager();
        $translator->setEventManager($events);

        $listener = $e->getApplication()->getServiceManager()->get('Common\Rbac\Navigation\IsAllowedListener');

        $events = $e->getApplication()->getEventManager();

        $events->getSharedManager()
            ->attach('Zend\View\Helper\Navigation\AbstractHelper', 'isAllowed', array($listener, 'accept'));
        $events->attach(
            $e->getApplication()->getServiceManager()->get('ZfcRbac\View\Strategy\UnauthorizedStrategy')
        );
        $events->attach(
            $e->getApplication()->getServiceManager()->get('ZfcRbac\View\Strategy\RedirectStrategy')
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Method to extract the language preference for a user.
     * At the moment this is taken from a cookie, with a key of lang.
     *
     * @return string locale string (default en_GB)
     */
    protected function getLanguageLocalePreference()
    {
        $locale = filter_input(INPUT_COOKIE, 'lang');

        if (empty($locale)) {

            $header = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');

            if (!empty($header)) {

                $locale = $this->formatLanguage($header);
            }
        }

        if (!in_array($locale, array_keys($this->getSupportedLanguages()))) {
            return 'en_GB';
        }

        return $locale;
    }

    /**
     * Format an AcceptLanguage into a locale for our translations
     *
     * @param string $language
     * @return string
     */
    private function formatLanguage($language)
    {
        $locale = \Locale::acceptFromHttp($language);

        // @todo this is wrong
        if (strlen($locale) == 2) {
            return strtolower($locale) . '_' . strtoupper($locale);
        }

        return $locale;
    }

    /**
     * Method to return a list of supported languages, ensures the language cannot be set to one for which
     * we have no translations for
     *
     * @return array of locales
     */
    protected function getSupportedLanguages()
    {
        return array(
            'en_GB' => 'English',
            'cy_CY' => 'Welsh',
            'cy_GB' => 'Welsh'
        );
    }
}
