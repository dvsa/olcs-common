<?php

namespace Common\View\Helper;

use Zend\I18n\View\Helper\Translate;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\RefData;

/**
 * Licence Checklist view helper
 */
class LicenceChecklist extends AbstractHelper implements FactoryInterface
{
    /**
     * @var Translate
     */
    private $translator;

    /**
     * Inject services
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->translator = $serviceLocator->get('translate');
        return $this;
    }

    /**
     * Return a prepared array with licence checklist sections details
     *
     * @return array
     */
    public function __invoke($type, $data)
    {
        $preparedData = [];
        switch ($type) {
            case RefData::LICENCE_CHECKLIST_TYPE_OF_LICENCE:
                $preparedData = $this->prepareTypeOfLicence($data['typeOfLicence']);
                break;
            case RefData::LICENCE_CHECKLIST_BUSINESS_TYPE:
                $preparedData = $this->prepareBusinessType($data['businessType']);
                break;
            case RefData::LICENCE_CHECKLIST_BUSINESS_DETAILS:
                $preparedData = $this->prepareBusinessDetails($data['businessDetails']);
                break;
            case RefData::LICENCE_CHECKLIST_ADDRESSES:
                $preparedData = $this->prepareAddresses($data['addresses']);
                break;
            case RefData::LICENCE_CHECKLIST_PEOPLE:
                $preparedData = $this->preparePeople($data['people']);
                break;
            case RefData::LICENCE_CHECKLIST_VEHICLES:
                $preparedData = $this->prepareVehicles($data['vehicles']);
                break;
        }
        return $preparedData;
    }

    /**
     * Prepare type of licence
     *
     * @param array $data data
     *
     * @return array
     */
    private function prepareTypeOfLicence($data)
    {
        return [
            [
                [
                    'value' => $this->translator->__invoke('continuations.type-of-licence.operating-from'),
                    'header' => true
                ],
                [
                    'value' => $data['operatingFrom']
                ]
            ],
            [
                [
                    'value' => $this->translator->__invoke('continuations.type-of-licence.type-of-operator'),
                    'header' => true
                ],
                [
                    'value' => $data['goodsOrPsv']
                ],
            ],
            [
                [
                    'value' => $this->translator->__invoke('continuations.type-of-licence.type-of-licence'),
                    'header' => true
                ],
                [
                    'value' => $data['licenceType']
                ]
            ]
        ];
    }

    /**
     * Prepare business type
     *
     * @param array $data data
     *
     * @return array
     */
    private function prepareBusinessType($data)
    {
        return [
            [
                [
                    'value' => $this->translator->__invoke('continuations.business-type.type-of-business'),
                    'header' => true
                ],
                [
                    'value' => $data['typeOfBusiness']
                ]
            ],
        ];
    }

    /**
     * Prepare business details
     *
     * @param array $data data
     *
     * @return array
     */
    private function prepareBusinessDetails($data)
    {
        $businessDetailsData = [];
        if (isset($data['companyNumber'])) {
            $businessDetailsData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.business-details.company-number'),
                    'header' => true
                ],
                [
                    'value' => $data['companyNumber']
                ]
            ];
        }
        if (isset($data['companyName'])) {
            $businessDetailsData[] = [
                ['value' => $data['organisationLabel'], 'header' => true],
                ['value' => $data['companyName']]
            ];
        }
        if (isset($data['tradingNames'])) {
            $businessDetailsData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.business-details.trading-names'),
                    'header' => true
                ],
                [
                    'value' => $data['tradingNames']
                ]
            ];
        }
        return $businessDetailsData;
    }

    /**
     * Prepare addresses
     *
     * @param array $data data
     *
     * @return array
     */
    private function prepareAddresses($data)
    {
        $addressesData = [];
        $addressesData[] = [
            [
                'value' => $this->translator->__invoke('continuations.addresses.correspondence-address.table.name'),
                'header' => true
            ],
            [
                'value' => $data['correspondenceAddress']
            ]
        ];
        if (isset($data['establishmentAddress'])) {
            $addressesData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.addresses.establishment-address.table.name'),
                    'header' => true
                ],
                [
                    'value' => $data['establishmentAddress']
                ]
            ];
        } else {
            $addressesData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.addresses.establishment-address.table.name'),
                    'header' => true
                ],
                [
                    'value' => $this->translator->__invoke('continuations.addresses.establishment-address.same'),
                ]
            ];
        }
        if (isset($data['primaryNumber'])) {
            $addressesData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.addresses.primary-number.table.name'),
                    'header' => true
                ],
                [
                    'value' => $data['primaryNumber']
                ]
            ];
        }
        if (isset($data['secondaryNumber'])) {
            $addressesData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.addresses.secondary-number.table.name'),
                    'header' => true
                ],
                [
                    'value' => $data['secondaryNumber']
                ]
            ];
        }

        return $addressesData;
    }

    /**
     * Prepare people
     *
     * @param array $data data
     *
     * @return array
     */
    private function preparePeople($data)
    {
        $persons = $data['persons'];
        if (is_array($persons) && count($persons) <= $data['displayPersonCount']) {
            $peopleData[] = [
                [
                    'value' => $this->translator->__invoke('continuations.people-section.table.name'),
                    'header' => true
                ],
                [
                    'value' => $this->translator->__invoke('continuations.people-section.table.date-of-birth'),
                    'header' => true
                ],
            ];
            $persons = $data['persons'];
            foreach ($persons as $person) {
                $peopleData[] = [
                    ['value' => $person['name']],
                    ['value' => $person['birthDate']]
                ];
            }
        } else {
            $peopleData[] = [
                ['value' => $data['header'], 'header' => true],
                ['value' => count($persons)]
            ];
        }
        return $peopleData;
    }

    /**
     * Prepare vehicles
     *
     * @param array $data data
     *
     * @return array
     */
    private function prepareVehicles($data)
    {
        $vehicles = $data['vehicles'];

        if (is_array($vehicles) && count($vehicles) <= $data['displayVehiclesCount']) {
            $header[] = [
                'value' => $this->translator->__invoke('continuations.vehicles-section.table.vrm'),
                'header' => true
            ];
            if ($data['isGoods']) {
                $header[] = [
                    'value' => $this->translator->__invoke('continuations.vehicles-section.table.weight'),
                    'header' => true
                ];
            }
            $vehicleData[] = $header;
            foreach ($vehicles as $vehicle) {
                $row = [];
                $row[] = ['value' => $vehicle['vrm']];
                if ($data['isGoods']) {
                    $row[] = ['value' => $vehicle['weight']];
                }
                $vehicleData[] = $row;
            }
        } else {
            $vehicleData[] = [
                ['value' => $data['header'], 'header' => true],
                ['value' => count($vehicles)]
            ];
        }
        return $vehicleData;
    }
}
