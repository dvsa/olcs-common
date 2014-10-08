<?php

/**
 * A trait that controllers can use to easily interact with the flash messenger.
 *
 * @package     olcscommon
 * @subpackage  utility
 * @author      Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Util;

/**
 * A trait that controllers can use to easily interact with the flash messenger.
 *
 * @package     olcscommon
 * @subpackage  utility
 * @author      Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
trait FlashMessengerTrait
{
    /**
     * returns an instance of the flash messenger plugin.
     *
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    public function getFlashMessenger()
    {
        return $this->plugin('FlashMessenger');
    }

    /**
     * Add message
     *
     * @param string $message
     * @param string $namespace
     */
    public function addMessage($message, $namespace = 'default')
    {
        $this->getFlashMessenger()->setNamespace($namespace)->addMessage($message)->setNamespace('default');

        return $this;
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

        return $this;
    }

    /**
     * Adds an warning message to the FlashMessenger.
     *
     * @param string $message The message
     *
     * @return \OlcsCommon\Utility\FlashMessengerTrait
     */
    public function addWarningMessage($message)
    {
        $this->getFlashMessenger()->addWarningMessage($message);

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

        return $this;
    }
}
