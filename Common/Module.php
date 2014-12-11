<?php

/**
 * ZF2 Module
 */

namespace Common;

use Zend\EventManager\EventManager;
use Common\Exception\ResourceNotFoundException;

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

        $events->attach(
            'missingTranslation',
            '\Common\Service\Translator\MissingTranslationProcessor::processEvent'
        );

        $translator->enableEventManager();
        $translator->setEventManager($events);

        $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            function (MvcEvent $e) {
                $exception = $e->getParam('exception');
                // If something throws an uncaught ResourceNotFoundException, return a 404
                if ($exception instanceof ResourceNotFoundException) {
                    $model = new ViewModel(
                        [
                            'message'   => $exception->getMessage(),
                            'reason'    => 'error-resource-not-found',
                            'exception' => $exception,
                        ]
                    );
                    $model->setTemplate('error/application_error');
                    $e->getViewModel()->addChild($model);
                    $e->getResponse()->setStatusCode(404);
                    $e->stopPropagation();
                    return $model;
                }
            }
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
     */
    private function formatLanguage($language)
    {
        $locale = \Locale::acceptFromHttp($language);

        if (strlen($locale) == 2) {
            return strtolower($locale) . '_' . strtoupper($locale);
        }
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
            'cy_CY' => 'Welsh'
        );
    }
}
