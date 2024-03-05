<?php

/**
 * Flash Messenger Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * Flash Messenger Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerHelperService
{
    public const NAMESPACE_PROMINENT_ERROR = 'prominent-error';

    protected $currentMessages = [
        'default' => [],
        'success' => [],
        'error' => [],
        'warning' => [],
        'info' => []
    ];

    /** @var FlashMessenger */
    protected $flashMessenger;

    /**
     * Create service instance
     *
     * @param FlashMessenger $flashMessenger
     *
     * @return FlashMessengerHelperService
     */
    public function __construct(
        FlashMessenger $flashMessenger
    ) {
        $this->flashMessenger = $flashMessenger;
    }

    public function addCurrentMessage($namespace, $message)
    {
        $this->currentMessages[$namespace][] = $message;
    }

    public function getCurrentMessages($namespace)
    {
        return $this->currentMessages[$namespace];
    }

    public function addCurrentSuccessMessage($message)
    {
        $this->addCurrentMessage('success', $message);
    }

    public function addCurrentErrorMessage($message)
    {
        $this->addCurrentMessage('error', $message);
    }

    public function addCurrentWarningMessage($message)
    {
        $this->addCurrentMessage('warning', $message);
    }

    public function addCurrentInfoMessage($message)
    {
        $this->addCurrentMessage('info', $message);
    }

    /**
     * Add a success message
     *
     * @param string $message
     * @return \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
     */
    public function addSuccessMessage($message)
    {
        return $this->getFlashMessenger()->addSuccessMessage($message);
    }

    /**
     * Add a error message
     *
     * @param string $message
     * @return \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
     */
    public function addErrorMessage($message)
    {
        return $this->getFlashMessenger()->addErrorMessage($message);
    }

    public function addProminentErrorMessage($message)
    {
        $namespace = $this->getFlashMessenger()->getNamespace();

        $this->getFlashMessenger()->setNamespace(self::NAMESPACE_PROMINENT_ERROR);
        $this->getFlashMessenger()->addMessage($message);

        $this->getFlashMessenger()->setNamespace($namespace);

        return $this;
    }

    /**
     * Add a warning message
     *
     * @param string $message
     * @return \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
     */
    public function addWarningMessage($message)
    {
        return $this->getFlashMessenger()->addWarningMessage($message);
    }


    /**
     * Add a info message
     *
     * @param string $message
     * @return \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
     */
    public function addInfoMessage($message)
    {
        return $this->getFlashMessenger()->addInfoMessage($message);
    }

    /**
     * Get the flash messenger
     *
     * @return \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
     */
    protected function getFlashMessenger()
    {
        return $this->flashMessenger;
    }

    public function addUnknownError()
    {
        return $this->addErrorMessage('unknown-error');
    }

    public function addConflictError()
    {
        return $this->addErrorMessage('conflict-error');
    }

    public function addCurrentUnknownError()
    {
        $this->addCurrentErrorMessage('unknown-error');
    }
}
