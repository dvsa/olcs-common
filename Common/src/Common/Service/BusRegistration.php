<?php

namespace Common\Service;

class BusRegistration
{
    const STATUS_NEW = 'breg_s_new';
    const STATUS_VAR = 'breg_s_var';
    const STATUS_CANCEL = 'breg_s_cancellation';

    protected $defaultAll = [
        // Reason for action text fields should all be empty
        'reasonSnRefused' => '',
        'reasonCancelled' => '',
        'reasonRefused' => '',
        // Withdrawn reason can be null; its here to override any value set in a variation/cancellation
        'withdrawnReason' => null,
        // At time of creation, we don't know if its short notice or not. Default to no.
        'isShortNotice' => 'N',
        // This is a new application/variation so hasn't been refused by short notice (yet)
        'shortNoticeRefused' => 'N',
        // Checks before granting should all default to no
        'copiedToLaPte' => 'N',
        'laShortNote' => 'N',
        'applicationSigned' => 'N',
        'opNotifiedLaPte' => 'N',
        // Trc conditions should also default to no/empty
        'trcConditionChecked' => 'N',
        'trcNotes' => '',
        // Timetable conditions should default to no
        'timetableAcceptable' => 'N',
        'mapSupplied' => 'N',
        // (Re)set dates to null
        'dateReceived' => null,
        'effectiveDate' => null,
        'endDate' => null,
        // These will be set to yes explicitly by the TXC processor, default it to no for the internal app
        'isTxcApp' => 'N',
        'ebsrRefresh' => 'N'
    ];

    protected $defaultNew = [
        'subsidised' => 'bs_no', //might this need to be a constant?
        'busNoticePeriod' => 2,
        'variationNo' => 0,
        'needNewStop' => 'N', //should this be moved to all? and the details field wiped?
        'hasManoeuvre' => 'N',
        'hasNotFixedStop' => 'N',
        // Reg number is generated based upon the licence and route number. empty by default.
        'regNo' => '',
        'routeNo' => 0,
        'useAllStops' => 'Y', //should probably default to yes
        'isQualityContract' => 'N',
        'isQualityPartnership' => 'N',
        'qualityPartnershipFacilitiesUsed' => 'N'
    ];

    protected $defaultShortNotice = [
        'bankHolidayChange' => 'N',
        'connectionChange' => 'N',
        'connectionDetail' => null,
        'holidayChange' => 'N',
        'holidayDetail' => null,
        'notAvailableChange' => 'N',
        'notAvailableDetail' => null,
        'policeChange' => 'N',
        'policeDetail' => null,
        'replacementChange' => 'N',
        'replacementDetail' => null,
        'specialOccasionChange' => 'N',
        'specialOccasionDetail' => null,
        'timetableChange' => 'N',
        'timetableDetail' => null,
        'trcChange' => 'N',
        'trcDetail' => null,
        'unforseenChange' => 'N',
        'unforseenDetail' => null,
    ];

    public function createNew($licence)
    {
        $data = array_merge($this->defaultAll, $this->defaultNew);
        $data['status'] = self::STATUS_NEW;
        $data['revertStatus'] = self::STATUS_NEW;

        $data['shortNotices'] = [$this->defaultShortNotice];

        $data['licence'] = $licence;

        return $data;
    }

    public function createVariation($previous)
    {
        $data = $previous;

        //unset database metadata
        $this->scrubEntity($data);
        if (isset($data['otherServices']) && is_array($data['otherServices'])) {
            foreach ($data['otherServices'] as &$otherService) {
                $this->scrubEntity($otherService);
            }
        }

        //new variation reasons will be required for a new variation
        unset($data['variationReasons']);

        $data['variationNo']++;
        $data['status'] = self::STATUS_VAR;
        $data['revertStatus'] = self::STATUS_VAR;

        //This is defined manyToOne in backend...
        $data['shortNotices'] = [$this->defaultShortNotice];
        $data['parent'] = $previous;

        //override columns which need different defaults for a variation
        $data = array_merge($data, $this->defaultAll);

        return $data;
    }

    public function createCancellation($previous)
    {
        $data = $this->createVariation($previous);

        $data['status'] = self::STATUS_CANCEL;
        $data['revertStatus'] = self::STATUS_CANCEL;

        return $data;
    }

    protected function scrubEntity(&$entity)
    {
        //unset database metadata
        unset(
            $entity['id'],
            $entity['version'],
            $entity['createdBy'],
            $entity['lastModifiedBy'],
            $entity['createdOn'],
            $entity['lastModifiedOn'],
            $entity['busRegId']
        );
    }

    public function getCascadeOptions()
    {
        return [
            //'_OPTIONS_' => [
                //'cascade' => [
                    'list' => [
                        'shortNotices' => [
                            'entity' => 'BusShortNotice',
                            'parent' => 'busReg'
                        ],
                        //currently handled by custom code, uncomment if/when removed
                        /*'otherServices' => [
                            'entity' => 'BusRegOtherService',
                            'parent' => 'busReg'
                        ]*/
                    ]
                //]
            //]
        ];
    }
}