<?php

namespace Common\Service\Data;

/**
 * Class Licence
 * @package Olcs\Service
 */
class Licence extends AbstractData implements AddressProviderInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $serviceName = 'Licence';

    /**
     * @param integer|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchLicenceData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData($id))) {
            $bundle = is_null($bundle) ? $this->getBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * Fetches an array of addresses for the licence. Queries the ContactDetails table
     * @param null $id
     * @param null $bundle
     * @return array
     */
    public function fetchAddressListData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData('addr_' .$id))) {
            $data = array();
            $bundle = is_null($bundle) ? $this->getAddressBundle() : $bundle;
            $addressData =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);

            if (isset($addressData['correspondenceCd']['address'])) {
                $data[] = $addressData['correspondenceCd']['address'];
            }
            if (isset($addressData['establishmentCd']['address'])) {
                $data[] = $addressData['establishmentCd']['address'];
            }
            if (isset($addressData['transportConsultantCd']['address'])) {
                $data[] = $addressData['transportConsultantCd']['address'];
            }

            $this->setData('addr_' .$id, $data);
        }
        return $this->getData('addr_' .$id);
    }

    /**
     * Fetches an array of OperatingCentres for the licence.
     * @param null $id
     * @param null $bundle
     * @return array
     */
    public function fetchOperatingCentreData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData('oc_' .$id))) {
            $data = array();

            $bundle = is_null($bundle) ? $this->getOperatingCentreBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);

            $this->setData('oc_' .$id, $data);
        }

        return $this->getData('oc_' . $id);
    }

    /**
     * Bundle to fetch all operating centres for a licence
     * @return array
     */
    public function getOperatingCentreBundle()
    {
        return array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre'
                    )
                )
            )
        );
    }

    /**
     * Bundle to fetch all addresses for licence
     * @return array
     */
    public function getAddressBundle()
    {
        $bundle = array(
            'children' => array(
                'correspondenceCd' => array(
                    'children' => array(
                        'address' => array(

                        )
                    )
                ),
                'establishmentCd' => array(
                    'children' => array(
                        'address' => array(

                        )
                    )
                ),
                'transportConsultantCd' => array(
                    'children' => array(
                        'address' => array(

                        )
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'cases' => array(
                    'children' => array(
                        'appeals' => array(
                            'children' => array(
                                'outcome' => array(),
                                'reason' => array(),
                            )
                        ),
                        'stays' => array(
                            'children' => array(
                                'stayType' => array(),
                                'outcome' => array()
                            )
                        )
                    )
                ),
                'correspondenceCd' => array(
                    'children' => array(
                        'address' => array()
                    )
                ),
                'status' => array(),
                'goodsOrPsv' => array(),
                'licenceType' => array(),
                'trafficArea' => array(),
                'organisation' => array(
                    'children' => array(
                        'organisationPersons' => array(),
                        'tradingNames' => array()
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
