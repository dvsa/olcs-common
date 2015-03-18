<?php

/**
 * Abstract Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Helper\FormHelperService;

/**
 * Abstract Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessDetails extends AbstractFormService
{
    public function getForm($orgType, $orgId)
    {
        $form = $this->getFormHelper()->createForm('Lva\BusinessDetails');

        $params = [
            'orgType' => $orgType,
            'orgId' => $orgId
        ];

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm($form, $params) {

        $fieldset = $form->get('data');

        switch ($params['orgType']) {
            case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationEntityService::ORG_TYPE_LLP:
                // no-op; the full form is fine
                break;
            case OrganisationEntityService::ORG_TYPE_SOLE_TRADER:
                $this->alterFormForNonRegisteredCompany($form);
                $this->getFormHelper()->remove($form, 'data->name');
                break;
            case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
                $this->alterFormForNonRegisteredCompany($form);
                $this->appendToLabel($fieldset->get('name'), '.partnership' );
                break;
            case OrganisationEntityService::ORG_TYPE_OTHER:
                $this->alterFormForNonRegisteredCompany($form);
                $this->getFormHelper()->remove($form, 'data->tradingNames');
                $this->appendToLabel($fieldset->get('name'), '.other');
                break;
        }
    }

    protected function appendToLabel($element, $append)
    {
        $this->getFormHelper()->alterElementLabel($element, $append, FormHelperService::ALTER_LABEL_APPEND);
    }

    /**
     * Make generic form alterations for non limited (or LLP) companies
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormForNonRegisteredCompany($form)
    {
        $this->getFormHelper()->remove($form, 'table')
            ->remove($form, 'data->companyNumber')
            ->remove($form, 'registeredAddress');
    }
}
