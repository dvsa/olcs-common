<?php
/**
 * A trait that controllers can use to easily interact with the flash messenger.
 *
 * @package     olcscommon
 * @subpackage  utility
 * @author      Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */

namespace Common\Util;

use \Zend\Log as Log;
use \Zend\Log\Logger as Logger;
use \Zend\Log\Writer as Writer;

trait LoggerTrait
{
    use Log\LoggerAwareTrait;

    /**
     * Returns an instantiated instance of Zend Log.
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (null === $this->logger) {

            $logger = $this->getServiceLocator()->get('Zend\Log');

            if (($logger instanceof \Zend\Log\Logger) !== true) {
                throw new \LogicException("Incorrect object. Expecting '\Zend\Log\Logger'");
            }

            $this->setLogger($logger);
        }
        return $this->logger;
    }

    /**
     * Logs a message to the defined logger.
     *
     * @param string $message
     * @param string $priority
     */
    public function log($message, $priority = Logger::INFO, $extra = array())
    {
        $this->getLogger()->log($priority, $message, $extra);
    }
}
