<?php
namespace Common;

class Module
{
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
                'ServiceApiResolver' => 'Common\Service\Api\ServiceApiResolver',
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
}
