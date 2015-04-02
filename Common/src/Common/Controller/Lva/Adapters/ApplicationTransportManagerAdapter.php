<?php

/**
 * Application Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    /**
     * Get Table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTable()
    {
        $table = parent::getTable();

        /* @var $service \Common\Service\Entity\TransportManagerApplicationEntityService */
        $service = $this->getServiceLocator()->get('Entity\TransportManagerApplication');

        $data = $service->getByApplicationWithHomeContactDetails($this->getApplicationId());

        $tableData = [];
        foreach ($data['Results'] as $row) {
            $tableData[] = [
                'id' => $row['id'],
                'name' => $row['transportManager']['homeCd']['person'],
                'status' => $row['tmApplicationStatus'],
                'email' => $row['transportManager']['homeCd']['emailAddress'],
                'dob' => $row['transportManager']['homeCd']['person']['birthDate'],
            ];
        }

        $table->loadData($tableData);

        return $table;
    }

    /**
     * Must this licence type have at least one Transport Manager
     *
     * @return bool
     */
    public function mustHaveAtLeastOneTm()
    {
        /* @var $service \Common\Service\Entity\ApplicationEntityService */
        $service = $this->getServiceLocator()->get('Entity\Application');

        $application = $service->getLicenceType($this->getApplicationId());

        $mustHaveTypes = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        return in_array($application['licenceType']['id'], $mustHaveTypes);
    }

    /**
     * Get the application ID
     *
     * @return int
     */
    private function getApplicationId()
    {
        $applicationId = (int) $this->controller->params('application');

        return $applicationId;
    }
}
