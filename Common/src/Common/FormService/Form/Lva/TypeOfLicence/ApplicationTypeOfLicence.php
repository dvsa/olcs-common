<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\FormServiceManager;
use Common\Rbac\Service\Permission;
use Common\Service\Helper\FormHelperService;
use Laminas\Form\Form;

/**
 * Application Type Of Licence
 */
class ApplicationTypeOfLicence extends AbstractTypeOfLicence
{
    protected FormHelperService $formHelper;

    protected Permission $permissionService;
    protected FormServiceManager $formServiceLocator;

    public function __construct(FormHelperService $formHelper, Permission $permissionService, FormServiceManager $formServiceLocator)
    {
        $this->formHelper = $formHelper;
        $this->permissionService = $permissionService;
        $this->formServiceLocator = $formServiceLocator;
    }
    protected function alterForm(Form $form, $params = [])
    {
        if ($this->permissionService->isInternalReadOnly()) {
            $this->disableLicenceType($form);
        }

        parent::alterForm($form, $params);

        $this->formServiceLocator->get('lva-application')->alterForm($form);
    }
}
