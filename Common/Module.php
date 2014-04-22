<?php

namespace Common;

class Module
{

    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator
            ->setLocale($this->getLanguageLocalePreference())
            ->setFallbackLocale('en_GB');
        $translator->addTranslationFilePattern('phparray', __DIR__ . '/config/language/', '%s.php');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'OlcsCustomForm' => function ($sm) {
                    return new \Common\Service\Form\OlcsCustomFormFactory($sm->get('Config'));
                },
                'Table' => '\Common\Service\Table\TableFactory',
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

                // Log file
                $fileWriter = new \Zend\Log\Writer\Stream('/tmp/olcsLogfile.log');
                $fileWriter->addFilter($filter);
                $log->addWriter($fileWriter);

                // Log to sys log - useful if file logging is not working.
                $sysLogWriter = new \Zend\Log\Writer\Syslog();
                $sysLogWriter->addFilter($filter);
                $log->addWriter($sysLogWriter);

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
        if (isset($_COOKIE['lang'])) {
            $lang = $_COOKIE['lang'];
            if (!empty($lang) && array_key_exists($lang, $this->getSupportedLanguages())) {
                return $lang;
            }
        }
        return 'en_GB';
    }

    /**
     * Method to return a list of supported languages, ensures the language cannot be set to one for which
     * we have no translations for
     *
     * @return Array of locales
     */
    protected function getsupportedLanguages()
    {
        return array('en_GB' => 'English',
            'cy_GB' => 'Welsh');
    }

}
