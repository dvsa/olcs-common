<?php

/**
 * Flash Messenger Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger;

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
     */
    public function addSuccessMessage($message)
    {
        $this->addMessage($message, FlashMessenger::NAMESPACE_SUCCESS);
    }

    /**
     * Add a error message
     *
     * @param string $message
     */
    public function addErrorMessage($message)
    {
        $this->addMessage($message, FlashMessenger::NAMESPACE_ERROR);
    }

    /**
     * Add a warning message
     *
     * @param string $message
     */
    public function addWarningMessage($message)
    {
        $this->addMessage($message, FlashMessenger::NAMESPACE_WARNING);
    }

    /**
     * Add a info message
     *
     * @param string $message
     */
    public function addInfoMessage($message)
    {
        $this->addMessage($message, FlashMessenger::NAMESPACE_INFO);
    }

    /**
     * Add a message to the defined namespace
     *
     * @param string $message
     * @param string $namespace
     */
    public function addMessage($message, $namespace = FlashMessenger::NAMESPACE_DEFAULT)
    {
        $this->getFlashMessenger()->setNamespace($namespace)->addMessage($message)
            ->setNamespace(FlashMessenger::NAMESPACE_DEFAULT);
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
