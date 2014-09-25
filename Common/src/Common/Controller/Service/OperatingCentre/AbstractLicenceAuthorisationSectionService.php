<?php

/**
 * Abstract Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;
use Common\Form\Elements\Validators\CantIncreaseValidator;

/**
 * Abstract Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLicenceAuthorisationSectionService extends AbstractAuthorisationSectionService
{
    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $actionService = 'LicenceOperatingCentre';

    /**
     * Action Identifier
     *
     * @var string
     */
    protected $actionIdentifier = 'licence';

    /**
     * Holds the section service
     *
     * @var string
     */
    protected $service = 'Licence';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences',
            'totAuthVehicles',
            'totAuthTrailers',
        ),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                    'name'
                )
            ),
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    private $totalAuthorisationsBundle = array(
        'properties' => array(
            'totAuthVehicles',
            'totAuthTrailers'
        )
    );

    private $ocAuthorisationsBundle = array(
        'properties' => array(
            'noOfVehiclesPossessed',
            'noOfTrailersPossessed'
        )
    );

    /**
     * Action save
     *
     * @param array $data
     * @param string $service
     * @return mixed
     */
    public function actionSave($data, $service = null)
    {
        $data['licence'] = $this->getIdentifier();

        return parent::actionSave($data, $service);
    }

    /**
     * Generic licence action form alterations
     *
     * @param \Zend\Form\Form $form
     */
    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        $filter = $form->getInputFilter();

        $data = $this->getVehicleAuthsForOperatingCentre($this->getActionId());

        foreach (['vehicles', 'trailers'] as $which) {
            $key = 'noOf' . ucfirst($which) . 'Possessed';

            if ($filter->get('data')->has($key)) {
                $this->attachCantIncreaseValidator($filter->get('data')->get($key), $which, $data[$key]);
            }
        }
    }

    /**
     * Extend the generic process load method
     *
     * @param array $data
     * @return array
     */
    public function processLoad($data)
    {
        // @NOTE Although we actually have licence data here, we are using the form from Application, so we need to
        //  pretend our licence info is the application info, and add the traffic area to a licence key
        $data['licence']['trafficArea'] = $data['trafficArea'];

        return parent::processLoad($data);
    }

    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        $form = parent::alterForm($form);

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
     * Get the traffic area section service
     *
     * @return \Common\Controller\Service\TrafficAreaSectionService
     */
    protected function getTrafficAreaSectionService()
    {
        return $this->getSectionService('LicenceTrafficArea');
    }

    /**
     * Get total authorisations for licence
     *
     * @param int $id
     * @return array
     */
    protected function getTotalAuthorisationsForLicence($id)
    {
        return $this->makeRestCall('Licence', 'GET', $id, $this->totalAuthorisationsBundle);
    }

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

        // @todo Get the application variation url form somewhere
        // $this->url()->fromRoute('application-variation')
        $message = $this->formatTranslation(
            '%s <a href="#">%s</a>',
            array(
                'cant-increase-' . $messageSuffix,
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
        return $this->makeRestCall('LicenceOperatingCentre', 'GET', $id, $this->ocAuthorisationsBundle);
    }
}
