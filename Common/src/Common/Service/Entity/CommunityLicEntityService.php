<?php

/**
 * Community Lic Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Entity\TrafficAreaEntityService;

/**
 * Community Lic Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicEntityService extends AbstractEntityService
{
    protected $entity = 'CommunityLic';

    const STATUS_PENDING = 'cl_sts_pending';
    const STATUS_ACTIVE = 'cl_sts_active';
    const STATUS_EXPIRED = 'cl_sts_expired';
    const STATUS_WITHDRAWN = 'cl_sts_withdrawn';
    const STATUS_SUSPENDED = 'cl_sts_suspended';
    const STATUS_VOID = 'cl_sts_void';
    const STATUS_RETURNDED = 'cl_sts_returned';
    const PREFIX_GB = 'UKGB';
    const PREFIX_NI = 'UKNI';

    protected $listBundle = array(
        'children' => array(
            'status'
        )
    );

    protected $licenceBundle = array(
        'children' => array(
            'status',
            'licence' => array(
                'children' => array(
                    'licenceType'
                )
            )
        )
    );

    public function getPendingForLicence($licenceId)
    {
        $query = array(
            'licence' => $licenceId,
            'specifiedDate' => 'NULL',
            'status' => self::STATUS_PENDING
        );

        return $this->getAll($query, $this->listBundle)['Results'];
    }

    /**
     * Get office copy
     *
     * @param int $licenceId
     * @return array
     */
    public function getOfficeCopy($licenceId)
    {
        $query = [
            'issueNo' => 0,
            'status' => $this->getValidStatusesForQuery(),
            'licence' => $licenceId
        ];
        $results = $this->get($query, $this->listBundle);
        if (count($results['Results'])) {
            $retv = $results['Results'][0];
        } else {
            $retv = null;
        }
        return $retv;
    }

    /**
     * Get valid licences
     *
     * @param int $licenceId
     * @return array
     */
    public function getValidLicences($licenceId)
    {
        $query = [
            'issueNo' => '!= 0',
            'status' => $this->getValidStatusesForQuery(),
            'licence' => $licenceId,
            'sort'  => 'issueNo',
            'order' => 'ASC'
        ];
        return $this->get($query, $this->listBundle);
    }

    /**
     * Get community licences where the status is pending, active or suspended.
     *
     * @param $licenceId The licence id.
     *
     * @return array
     */
    public function getValidLicencesForLicenceStatus($licenceId)
    {
        $valid = array(
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_SUSPENDED
        );

        $query = [
            'status' => 'IN ["' . implode('","', $valid) . '"]',
            'licence' => $licenceId,
        ];

        return $this->get($query, $this->listBundle);
    }

    /**
     * Get valid statuses for query
     *
     * @return string
     */
    private function getValidStatusesForQuery()
    {
        $valid = [
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_WITHDRAWN,
            self::STATUS_SUSPENDED
        ];
        return 'IN ["' . implode('","', $valid) . '"]';
    }

    /**
     * Get active licences
     *
     * @param int $licenceId
     * @return array
     */
    public function getActiveLicences($licenceId)
    {
        $query = [
            'status' => self::STATUS_ACTIVE,
            'licence' => $licenceId,
            'sort'  => 'issueNo',
            'order' => 'ASC'
        ];
        return $this->get($query, $this->listBundle);
    }

    /**
     * Add office copy
     *
     * @param array $data
     * @param int $licenceId
     */
    public function addOfficeCopy($data, $licenceId)
    {
        $additionalData = [
            'licence' => $licenceId,
            'serialNoPrefix' => $this->getSerialNoPrefixFromTrafficArea($licenceId),
            'issueNo' => 0
        ];
        $this->save(array_merge($data, $additionalData));
    }

    /**
     * Insert several community licence at once
     *
     * @param array $data
     * @param int $licenceId
     * @param int $totalCommunityLicences
     */
    public function addCommunityLicences($data, $licenceId, $totalCommunityLicences)
    {
        $validLicences = $this->getValidLicences($licenceId);
        $startIssueNo = $validLicences['Count'] ?
            $validLicences['Results'][$validLicences['Count'] - 1]['issueNo'] + 1 : 1;

        $data['serialNoPrefix'] = $this->getSerialNoPrefixFromTrafficArea($licenceId);
        $data['licence'] = $licenceId;

        $dataToSave = [];
        for ($i = 1; $i <= $totalCommunityLicences; $i++) {
            $data['issueNo'] = $startIssueNo++;
            $dataToSave[] = $data;
        }
        $dataToSave['_OPTIONS_'] = ['multiple' => true];
        return $this->save($dataToSave);
    }

    /**
     * Insert several community licences at once with specific issue numbers
     *
     * @param array $data
     * @param int $licenceId
     * @param int $issueNos
     */
    public function addCommunityLicencesWithIssueNos($data, $licenceId, $issueNos)
    {
        $data['serialNoPrefix'] = $this->getSerialNoPrefixFromTrafficArea($licenceId);
        $data['licence'] = $licenceId;

        $dataToSave = [];
        foreach ($issueNos as $issueNo) {
            $data['issueNo'] = $issueNo;
            $dataToSave[] = $data;
        }
        $dataToSave['_OPTIONS_'] = ['multiple' => true];
        return $this->save($dataToSave);
    }

    public function getWithLicence($id)
    {
        return $this->get($id, $this->licenceBundle);
    }

    /**
     * Get licences by multiple ids.
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids)
    {
        $data = [];
        // Doctrine implementation won't let us do: $this->getAll(['id' => 'IN ' . json_encode($ids)]);
        foreach ($ids as $id) {
            $data[] = $this->get($id);
        }
        return $data;
    }

    protected function getSerialNoPrefixFromTrafficArea($licenceId)
    {
        $trafficArea = $this->getServiceLocator()->get('Entity\Licence')->getTrafficArea($licenceId);
        return ($trafficArea['id'] === TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
            ? self::PREFIX_NI : self::PREFIX_GB;
    }
}
