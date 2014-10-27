<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

use Common\Form\Elements\Validators\CantIncreaseValidator;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

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
            '%s <a href="%s">%s</a>',
            array(
                'cant-increase-' . $messageSuffix,
                $this->url()->fromRoute('create_variation', ['id' => $this->getIdentifier()]),
                'create-variation'
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

    /**
     * Get total authorisations for licence
     *
     * @param int $id
     * @return array
     */
    protected function getTotalAuthorisationsForLicence($id)
    {
        return $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getTotalAuths($id);
    }

    /**
     * Generic licence action form alterations
     *
     * @param \Zend\Form\Form $form
     */
    public function alterActionForm(Form $form)
    {
        $filter = $form->getInputFilter();

        $data = $this->getVehicleAuthsForOperatingCentre($this->params('child_id'));

        foreach (['vehicles', 'trailers'] as $which) {
            $key = 'noOf' . ucfirst($which) . 'Possessed';

            if ($filter->get('data')->has($key)) {
                $this->attachCantIncreaseValidator($filter->get('data')->get($key), $which, $data[$key]);
            }
        }

        return $form;
    }

    protected function commonAlterForm(Form $form)
    {
        $data = $this->getTotalAuthorisationsForLicence($this->getIdentifier());

        $filter = $form->getInputFilter();

        foreach (['vehicles', 'trailers'] as $which) {
            $key = 'totAuth' . ucfirst($which);

            if ($filter->get('data')->has($key)) {
                $this->attachCantIncreaseValidator(
                    $filter->get('data')->get($key),
                    'total-' . $which,
                    $data[$key]
                );
            }
        }

        return $form;
    }

    public function addAction()
    {
        $view = new ViewModel(
            array(
                'licence' => $this->getIdentifier()
            )
        );
        $view->setTemplate('licence/add-authorisation');

        return $this->render($view);
    }


}
