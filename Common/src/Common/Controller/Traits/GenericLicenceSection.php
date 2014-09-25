<?php

/**
 * Generic Licence Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

/**
 * Generic Licence Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericLicenceSection
{
    /**
     * Licence type
     *
     * @var string
     */
    private $licenceType = null;

    /**
     * Cache licence data requests
     *
     * @var array
     */
    private $licenceData = array();

    /**
     * Check if is psv
     *
     * @var boolean
     */
    protected $isPsv = null;

    /**
     * Licence data service
     *
     * @var string
     */
    protected $licenceDataService = 'Licence';

    /**
     * Holds the licenceDataBundle
     *
     * @var array
     */
    protected $licenceDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'niFlag',
            'licNo'
        ),
        'children' => array(
            'goodsOrPsv' => array(
                'properties' => array(
                    'id'
                )
            ),
            'licenceType' => array(
                'properties' => array(
                    'id'
                )
            ),
            'organisation' => array(
                'children' => array(
                    'type' => array(
                        'properties' => array(
                            'id'
                        )
                    )
                )
            )
        )
    );

    protected function getLicenceDataService()
    {
        return $this->licenceDataService;
    }

    protected function getLicenceDataBundle()
    {
        return $this->licenceDataBundle;
    }

    /**
     * Check if application is psv
     *
     * GetAccessKeys "should" always be called first so psv should be set
     *
     * @return boolean
     */
    protected function isPsv()
    {
        return $this->isPsv;
    }

    /**
     * Get the licence data
     *
     * @return array
     */
    protected function doGetLicenceData()
    {
        if (empty($this->licenceData)) {

            $results = $this->makeRestCall(
                $this->getLicenceDataService(),
                'GET',
                array('id' => $this->getIdentifier()),
                $this->getLicenceDataBundle()
            );

            $this->licenceData = $results;
        }

        return $this->licenceData;
    }
}
