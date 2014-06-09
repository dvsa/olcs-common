<?php

/**
 * Logger trait
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\View\Helper\Traits;

use Common\Util\LoggerTrait as CommonLoggerTrait;

/**
 * Logger trait
 *
 * @author Someone <someone@valtech.co.uk>
 */
trait Logger
{
    use CommonLoggerTrait;

    /**
     * Logs a message to the defined logger.
     *
     * @param string $message
     * @param string $priority
     */
    public function log($message, $priority = Logger::INFO, $extra = array())
    {
        unset($extra);
        // For now log to syslog - there's no way to get the service locator
        // to gain access to the main logger in a view helper.

        syslog($priority, $message);
    }
}
