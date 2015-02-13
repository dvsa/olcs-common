<?php

/**
 * Community Lic Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

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
     * Add office copy
     *
     * @param array $data
     * @param int $licenceId
     */
    public function addOfficeCopy($data, $licenceId)
    {
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getById($licenceId);
        $additionalData = [
            'licence' => $licenceId,
            'serialNoPrefix' => $licence['niFlag'] === 'Y' ? self::PREFIX_NI : self::PREFIX_GB,
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

        $licence = $this->getServiceLocator()->get('Entity\Licence')->getById($licenceId);
        $data['serialNoPrefix'] = $licence['niFlag'] === 'Y' ? self::PREFIX_NI : self::PREFIX_GB;
        $data['licence'] = $licenceId;

        $dataToSave = [];
        for ($i = 1; $i <= $totalCommunityLicences; $i++) {
            $data['issueNo'] = $startIssueNo++;
            $dataToSave[] = $data;
        }
        $dataToSave['_OPTIONS_'] = ['multiple' => true];
        $this->save($dataToSave);
    }
}
