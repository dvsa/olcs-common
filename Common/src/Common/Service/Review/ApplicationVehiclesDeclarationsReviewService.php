<?php

/**
 * Application Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Vehicles Declarations Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesDeclarationsReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $multiItems = [];

        // All options relating to having small vehicles
        if ($data['totAuthSmallVehicles'] > 0) {

            // 15b[i]
            if ($data['licence']['trafficArea']['isScotland'] == false) {
                $multiItems['15b'][] = $this->addSection15b1($data);

                // 15b[ii]
                if ($data['psvOperateSmallVhl'] === 'Y') {
                    $multiItems['15b'][] = $this->addSection15b2($data);
                } else {
                    // 15c/d
                    $multiItems['15cd'] = $this->addSection15cd($data);
                }
            } else {
                // 15c/d
                $multiItems['15cd'] = $this->addSection15cd($data);
            }
        }

        if ($data['totAuthSmallVehicles'] == 0
            && ($data['totAuthMediumVehicles'] > 0 || $data['totAuthLargeVehicles'] > 0)) {

            $multiItems['15e'][] = $this->addSection15e($data);
        }

        if ($data['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED
            && $data['totAuthMediumVehicles'] > 0) {

            $multiItems['8b'][] = $this->addSection8b1($data);
            $multiItems['8b'][] = $this->addSection8b2($data);
        }

        $multiItems['15f'][] = $this->addSection15f1($data);

        if ($data['psvLimousines'] === 'Y') {
            $multiItems['15g'][] = $this->addSection15g();
        } else {
            $multiItems['15f'][] = $this->addSection15f2();
        }

        return [
            'multiItems' => $multiItems
        ];
    }

    protected function addSection15b1($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-15b1',
            'value' => $this->formatYesNo($data['psvOperateSmallVhl'])
        ];
    }

    protected function addSection15b2($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-15b2',
            'noEscape' => true,
            'value' => $this->formatText($data['psvSmallVhlNotes'])
        ];
    }

    protected function addSection15cd($data)
    {
        return [
            [
                'label' => 'application-review-vehicles-declarations-15cd',
                'value' => $this->formatConfirmed($data['psvSmallVhlConfirmation'])
            ],
            [
                'label' => 'markup-application_vehicle-safety_undertakings-smallVehiclesUndertakingsScotland',
                'noEscape' => true,
                'value' => '<h4>Undertakings</h4>'
                    . $this->translate('markup-application_vehicle-safety_undertakings-smallVehiclesUndertakings')
            ]
        ];
    }

    protected function addSection15e($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-15e',
            'value' => $this->formatConfirmed($data['psvNoSmallVhlConfirmation'])
        ];
    }

    protected function addSection8b1($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-8b1',
            'value' => $this->formatConfirmed($data['psvMediumVhlConfirmation'])
        ];
    }

    protected function addSection8b2($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-8b2',
            'noEscape' => true,
            'value' => $this->formatText($data['psvMediumVhlNotes'])
        ];
    }

    protected function addSection15f1($data)
    {
        return [
            'label' => 'application-review-vehicles-declarations-15f1',
            'value' => $this->formatYesNo($data['psvLimousines'])
        ];
    }

    protected function addSection15f2()
    {
        return [
            'label' => 'application-review-vehicles-declarations-15f2',
            'value' => $this->formatConfirmed('Y')
        ];
    }

    protected function addSection15g()
    {
        return [
            'label' => 'application-review-vehicles-declarations-15g',
            'value' => $this->formatConfirmed('Y')
        ];
    }
}
