<?php

/**
 * Abstract Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\BusinessDetails;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;
use Common\Service\Helper\FormHelperService;

/**
 * Abstract Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessDetails extends AbstractFormService
{
    public function getForm($orgType, $hasInforceLicences)
    {
        $form = $this->getFormHelper()->createForm('Lva\BusinessDetails');

        $params = [
            'orgType' => $orgType,
            'hasInforceLicences' => $hasInforceLicences
        ];

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm($form, $params)
    {
        switch ($params['orgType']) {
            case RefData::ORG_TYPE_REGISTERED_COMPANY:
            case RefData::ORG_TYPE_LLP:
                // no-op; the full form is fine
                break;
            case RefData::ORG_TYPE_SOLE_TRADER:
                $this->alterFormForNonRegisteredCompany($form);
                $this->getFormHelper()->remove($form, 'data->name');
                break;
            case RefData::ORG_TYPE_PARTNERSHIP:
                $this->alterFormForNonRegisteredCompany($form);
                $this->appendToLabel($form->get('data')->get('name'), '.partnership');
                break;
            case RefData::ORG_TYPE_OTHER:
                $this->alterFormForNonRegisteredCompany($form);
                $this->getFormHelper()->remove($form, 'data->tradingNames');
                $this->appendToLabel($form->get('data')->get('name'), '.other');
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
     * @param \Laminas\Form\Form $form
     */
    protected function alterFormForNonRegisteredCompany($form)
    {
        $this->getFormHelper()->remove($form, 'table')
            ->remove($form, 'data->companyNumber')
            ->remove($form, 'registeredAddress');
    }
}
