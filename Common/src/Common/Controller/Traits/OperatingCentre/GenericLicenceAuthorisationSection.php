<?php

/**
 * Generic Licence Authorisation Section
 *
 * Internal/External - Licence Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * Generic Licence Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericLicenceAuthorisationSection
{
    use GenericAuthorisationSection;

    protected $sharedBespokeSubActions = array(
        'add'
    );

    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $sharedActionService = 'LicenceOperatingCentre';

    /**
     * Holds the section service
     *
     * @var string
     */
    protected $sharedService = 'Licence';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $sharedDataBundle = array(
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

    /**
     * Traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'properties' => array(),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                    'name'
                )
            )
        )
    );

    /**
     * Licence details for traffic area
     *
     * @var array
     */
    protected $licenceDetailsForTrafficAreaBundle = array(
        'properties' => array(
            'id',
            'version'
        )
    );

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->addVariationInfoMessage();

        return $this->renderSection();
    }

    /**
     * Get bespoke sub actions
     *
     * @return array
     */
    protected function getBespokeSubActions()
    {
        return $this->sharedBespokeSubActions;
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        $this->viewTemplateName = 'licence/add-authorisation';

        return $this->renderSection();
    }

    /**
     * Retrieve the relevant table data as we want to render it on the review summary page
     * Note that as with most controllers this is the same data we want to render on the
     * normal form page, hence why getFormTableData (declared later) simply wraps this
     */
    protected static function getSummaryTableData($id, $context, $tableName)
    {
        $data = $context->makeRestCall(
            'LicenceOperatingCentre',
            'GET',
            array('licence' => $id),
            static::$tableDataBundle
        );

        return static::formatSummaryTableData($data);
    }

    /**
     * Get operating centres count
     *
     * @return int
     */
    protected function getOperatingCentresCount()
    {
        $operatingCentres = $this->makeRestCall(
            $this->sharedActionService,
            'GET',
            array('licence' => $this->getIdentifier()),
            $this->ocCountBundle
        );

        return $operatingCentres['Count'];
    }

    /**
     * Do action save
     *
     * @param array $data
     * @param string $service
     * @return mixed
     */
    protected function doActionSave($data, $service)
    {
        $data['licence'] = $this->getIdentifier();

        return parent::actionSave($data, $service);
    }

    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $this->doAlterForm(parent::alterForm($form));
    }

    /**
     * Add variation info message
     */
    protected function addVariationInfoMessage()
    {
        $this->addCurrentMessage(
            $this->formatTranslation(
                '%s <a href="' . $this->url()->fromRoute('application-variation') . '">%s</a>',
                array(
                    'variation-application-text',
                    'variation-application-link-text'
                )
            ),
            'info'
        );
    }

    /**
     * Get licence details to update traffic area
     *
     * @return array
     */
    protected function getLicenceDetailsToUpdateTrafficArea()
    {
        return $this->makeRestCall(
            'Licence',
            'GET',
            array(
                'id' => $this->getIdentifier()
            ),
            $this->licenceDetailsForTrafficAreaBundle
        );
    }

    /**
     * Extend the generic process load method
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        // @NOTE Although we actually have licence data here, we are using the form from Application, so we need to
        //  pretend our licence info is the application info, and add the traffic area to a licence key
        $data['licence']['trafficArea'] = $data['trafficArea'];

        return $this->doProcessLoad($data);
    }

    /**
     * Get Traffic Area information for current application
     *
     * @return array
     */
    protected function getTrafficArea()
    {
        if (empty($this->trafficArea)) {
            $licence = $this->makeRestCall(
                'Licence',
                'GET',
                array(
                    'id' => $this->getIdentifier(),
                ),
                $this->trafficAreaBundle
            );

            if (isset($licence['trafficArea'])) {
                $this->trafficArea = $licence['trafficArea'];
            }
        }

        return $this->trafficArea;
    }
}
