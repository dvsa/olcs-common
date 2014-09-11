<?php

/**
 * ZF2 Module
 */

namespace Common;

use Zend\EventManager\EventManager;

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

        $events = new EventManager();
        $events->attach(
            'missingTranslation',
            '\Common\Service\Translator\MissingTranslationProcessor::processEvent'
        );

        $translator->enableEventManager();

        $translator->setEventManager($events);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Common\Service\Data\RefData' => 'Common\Service\Data\RefData',
                'OlcsCustomForm' => function ($sm) {
                    return new \Common\Service\Form\OlcsCustomFormFactory($sm->get('Config'));
                },
                'Script' => function ($sm) {
                    return new \Common\Service\Script\ScriptFactory($sm->get('Config'));
                },
                'Table' => '\Common\Service\Table\TableFactory',
                'FileUploader' => '\Common\Service\File\FileUploaderFactory',
                'ServiceApiResolver' => 'Common\Service\Api\ServiceApiResolver',
                'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
                'Zend\Log' => function ($sm) {
                    $log = new \Zend\Log\Logger();

                    /**
                     * In development / integration - we log everything.
                     * In production, our logging
                     * is restricted to \Zend\Log\Logger::ERR and above.
                     *
                     * For logging priorities, see:
                     * @see http://www.php.net/manual/en/function.syslog.php#refsect1-function.syslog-parameters
                     */
                    $filter = new \Zend\Log\Filter\Priority(LOG_DEBUG);

                    try {
                        // Log file
                        $fileWriter = new \Zend\Log\Writer\Stream('/tmp/olcsLogfile.log');
                        $fileWriter->addFilter($filter);
                        $log->addWriter($fileWriter);
                        $hasWriter = true;
                    } catch (\Exception $ex) {
                        $hasWriter = false;
                    }

                    try {
                        // Log to sys log - useful if file logging is not working.
                        $sysLogWriter = new \Zend\Log\Writer\Syslog();
                        $sysLogWriter->addFilter($filter);
                        $log->addWriter($sysLogWriter);
                    } catch (\Exception $ex) {
                        // Only throw this exception if we have no writers
                        if ($hasWriter == false) {
                            throw $ex;
                        }
                    }

                    return $log;
                }
            ),
            'aliases' => array(
                'translator' => 'MvcTranslator',
            ),
        );
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
