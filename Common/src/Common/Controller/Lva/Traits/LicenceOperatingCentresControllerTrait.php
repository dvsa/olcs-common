<?php

/**
 * Shared logic between Licence Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Form\Elements\Validators\CantIncreaseValidator;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

/**
 * Shared logic between Licence Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait LicenceOperatingCentresControllerTrait
{
    /**
     * Attach a can't increase validator
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
                $this->url()->fromRoute('create_variation', ['licence' => $this->getLicenceId()]),
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
            $key = 'noOf' . ucfirst($which) . 'Required';

            if ($filter->get('data')->has($key)) {
                $this->attachCantIncreaseValidator($filter->get('data')->get($key), $which, $data[$key]);
            }
        }

        return $form;
    }

    /**
     * Common form alterations
     *
     * @param \Zend\Form\Form $form
     */
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

    /**
     * Override add action to show variation warning
     */
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

    /**
     * Format crud data for save
     *
     * @param array $data
     */
    protected function formatCrudDataForSave($data)
    {
        // @see https://jira.i-env.net/browse/OLCS-5555
        unset($data['operatingCentre']['addresses']);

        return $data;
    }

    /**
     * Disable conditional validation
     *
     * For licences, we don't want to validate any auth
     * totals if they haven't been altered
     */
    protected function disableConditionalValidation(Form $form)
    {
        $data = $this->getRequest()->getPost();
        $data = isset($data['data']) ? $data['data'] : [];

        // allow for *all* totals to have been submitted; in reality
        // the values will be a subset of this dependent on goods/psv
        $submitted = [
            'totAuthLargeVehicles',
            'totAuthMediumVehicles',
            'totAuthSmallVehicles',
            'totAuthVehicles',
            'totAuthTrailers'
        ];

        // we need to fetch our entity details and
        // as long as all relevant totals match, disable their
        // validation

        $totals = $this->getTotalAuthorisationsForLicence($this->getIdentifier());

        $formHelper = $this->getServiceLocator()
            ->get('Helper\Form');
        $filter = $form->getInputFilter()->get('data');

        foreach ($submitted as $property) {
            if (isset($data[$property]) && (int)$data[$property] === (int)$totals[$property]) {
                $formHelper->disableValidation(
                    $filter->get($property)
                );
            }
        }
    }
}
