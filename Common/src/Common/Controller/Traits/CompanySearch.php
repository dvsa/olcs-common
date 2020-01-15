<?php


namespace Common\Controller\Traits;

use Common\Form\Form;
use Common\Service\Helper\FormHelperService as FormHelper;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\ByNumber;

trait CompanySearch
{
    public static $companyNameLength = 8;

    protected function populateCompanyDetails(FormHelper $formHelper, $form, $detailsFieldset, $addressFieldset, $companyNumber): Form
    {
        try {
            $response = $this->handleQuery(ByNumber::create(['companyNumber' => $companyNumber]));
        } catch (NotFoundException $exception) {
            $formHelper->setCompanyNotFoundError($form, $detailsFieldset);
            return $this->renderForm($form);
        }

        if ($response->isOk()) {
            $formHelper->processCompanyNumberLookupForm(
                $form,
                $response->getResult(),
                $detailsFieldset,
                $addressFieldset
            );
        } else {
            $formHelper->setCompanyNotFoundError($form, $detailsFieldset);
        }

        return $form;
    }


    private function isValidCompanyNumber($companyNumber)
    {
        return strlen($companyNumber) === self::$companyNameLength;
    }
}
