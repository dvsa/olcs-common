<?php

/**
 * Flash Messenger Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Flash Messenger Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerHelperService extends AbstractHelperService
{
    /**
     * Add a success message
     *
     * @param string $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    public function addSuccessMessage($message)
    {
        return $this->getFlashMessenger()->addSuccessMessage($message);
    }

    /**
     * Add a error message
     *
     * @param string $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    public function addErrorMessage($message)
    {
        return $this->getFlashMessenger()->addErrorMessage($message);
    }

    /**
     * Add a warning message
     *
     * @param string $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    public function addWarningMessage($message)
    {
        return $this->getFlashMessenger()->addWarningMessage($message);
    }

    /**
     * Add a info message
     *
     * @param string $message
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    public function addInfoMessage($message)
    {
        return $this->getFlashMessenger()->addInfoMessage($message);
    }

    /**
     * Get the flash messenger
     *
     * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected function getFlashMessenger()
    {
        return $this->getServiceLocator()->get('ControllerPluginManager')->get('FlashMessenger');
    }
}
