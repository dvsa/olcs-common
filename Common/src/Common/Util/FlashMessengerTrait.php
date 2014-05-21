<?php
/**
 * A trait that controllers can use to easily interact with the flash messenger.
 *
 * @package     olcscommon
 * @subpackage  utility
 * @author      Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */

namespace Common\Util;

use \Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessenger;

trait FlashMessengerTrait
{
    //use LoggerTrait;

    /**
     * returns an instance of the flash messenger plugin.
     *
     * @return FlashMessenger
     */
    public function getFlashMessenger()
    {
        $this->log('FlashMessenger Plugin Requested from Controller', LOG_DEBUG);

        $plugin = $this->plugin('FlashMessenger');

        $this->log('FlashMessenger Plugin Loaded to Controller', LOG_DEBUG);
        $this->log('FlashMessenger Namespace is: ' . $plugin->getNamespace(), LOG_DEBUG);

        return $plugin;
    }

    /**
     * Adds an information message to the FlashMessenger.
     *
     * @param string $message The message
     *
     * @return \OlcsCommon\Utility\FlashMessengerTrait
     */
    public function addInfoMessage($message)
    {
        $this->getFlashMessenger()->addInfoMessage($message);
        $this->log(sprintf("FlashMessenger Info Message Registered: '%s'", $message), LOG_DEBUG);
        return $this;
    }

    /**
     * Adds an error message to the FlashMessenger.
     *
     * @param string $message The message
     *
     * @return \OlcsCommon\Utility\FlashMessengerTrait
     */
    public function addErrorMessage($message)
    {
        $this->getFlashMessenger()->addErrorMessage($message);
        $this->log(sprintf("FlashMessenger Error Message Registered: '%s'", $message), LOG_DEBUG);
        return $this;
    }

    /**
     * Adds a success message to the FlashMessenger.
     *
     * @param string $message The message
     *
     * @return \OlcsCommon\Utility\FlashMessengerTrait
     */
    public function addSuccessMessage($message)
    {
        $this->getFlashMessenger()->addSuccessMessage($message);
        $this->log(sprintf("FlashMessenger Success Message Registered: '%s'", $message), LOG_DEBUG);
        return $this;
    }
}
