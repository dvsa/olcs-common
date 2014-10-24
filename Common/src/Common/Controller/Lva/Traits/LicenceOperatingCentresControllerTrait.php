<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

use Common\Form\Elements\Validators\CantIncreaseValidator;

/**
 */
trait LicenceOperatingCentresControllerTrait
{
    /**
     * Attach a cant increase validator
     *
     * @param Input $input
     * @param string $messageSuffix
     * @param int $previousValue
     */
    protected function attachCantIncreaseValidator($input, $messageSuffix, $previousValue)
    {
        $validatorChain = $input->getValidatorChain();

        $cantIncreaseValidator = new CantIncreaseValidator();

        $message = $this->getServiceLocator()->get('Helper\Translation')->formatTranslation(
            '%s <a href="#">%s</a>',
            array(
                'cant-increase-' . $messageSuffix,
                $this->url()->fromRoute('create_variation', ['id' => $this->getIdentifier()])
            )
        );

        $cantIncreaseValidator->setGenericMessage($message);
        $cantIncreaseValidator->setPreviousValue($previousValue);

        $validatorChain->attach($cantIncreaseValidator);
    }

    /**
     * Get the vehicle auths for the OC (given a licence_operating_Centre_id)
     *
     * @param int $id
     * @return array
     */
    protected function getVehicleAuthsForOperatingCentre($id)
    {
        return $this->getServiceLocator()
            ->get('Entity\LicenceOperatingCentre')
            ->getVehicleAuths($id);
    }
}
