<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;

/**
 * Class LicenceOperatingCentre
 * @package Olcs\Service
 */
class LicenceOperatingCentre extends AbstractData implements FactoryInterface, ListDataInterface
{
    use LicenceServiceTrait;

    const OUTPUT_TYPE_FULL = 1;
    const OUTPUT_TYPE_PARTIAL = 2;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var int
     */
    protected $outputType = self::OUTPUT_TYPE_FULL;

    /**
     * @var string
     */
    protected $serviceName = 'LicenceOperatingCentre';

    /**
     * @param integer|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        $id = $this->getId();

        if (is_null($this->getData($id))) {
            $data = array();
            $rawData =  $this->getLicenceService()->fetchOperatingCentreData($this->getId(), $this->getBundle());
            if (is_array($rawData['operatingCentres'])) {
                $outputType = $this->getOutputType();
                foreach ($rawData['operatingCentres'] as $licenceOperatingCentre) {
                    if ($outputType == self::OUTPUT_TYPE_PARTIAL) {
                        $data[$licenceOperatingCentre['operatingCentre']['id']] =
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine1'] . ', ' .
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine2'] . ', ' .
                            $licenceOperatingCentre['operatingCentre']['address']['town'];
                    } else {
                        $data[$licenceOperatingCentre['operatingCentre']['id']] =
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine1'] . ', ' .
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine2'] . ', ' .
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine3'] . ' ' .
                            $licenceOperatingCentre['operatingCentre']['address']['addressLine4'] . ' ' .
                            $licenceOperatingCentre['operatingCentre']['address']['postcode'];
                    }
                }
            }
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre' => array(
                            'children' => array(
                                'address'
                            )
                        )
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->getLicenceService()->getId();
    }

    /**
     * @return int
     */
    public function getOutputType()
    {
        return $this->outputType;
    }

    /**
     * @param int $outputType
     */
    public function setOutputType($outputType)
    {
        $this->outputType = $outputType;
    }
}
