<?php

/**
 * Lva Controller Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Stubs;

use Common\Controller\Lva\AbstractController;

/**
 * Lva Controller Stub
 *
 * @NOTE allows us to test abstract controller logic in isolation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LvaControllerStub extends AbstractController
{
    public function setLva($lva)
    {
        $this->lva = $lva;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function callPreDispatch()
    {
        return $this->preDispatch();
    }

    public function callIsButtonPressed($button)
    {
        return $this->isButtonPressed($button);
    }

    public function callHasConditions()
    {
        return $this->hasConditions();
    }

    public function callGetTypeOfLicenceData()
    {
        return $this->getTypeOfLicenceData();
    }

    public function callPostSave($section)
    {
        return $this->postSave($section);
    }

    public function callAlterFormForLva($form)
    {
        return $this->alterFormForLva($form);
    }

    public function callAddCurrentMessage($message, $namespace = 'default')
    {
        return $this->addCurrentMessage($message, $namespace);
    }

    public function callAttachCurrentMessages()
    {
        return $this->attachCurrentMessages();
    }

    public function callReload()
    {
        return $this->reload();
    }

    public function callGetAccessibleSections($keysOnly = true)
    {
        return $this->getAccessibleSections($keysOnly);
    }
}
