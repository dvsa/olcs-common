<?php

/**
 * Flatten Transport Manager Applications data
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\Service\Table\DataMapper;

/**
 * Flatten Transport Manager Applications data
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DashboardTmApplications implements DataMapperInterface
{
    /**
     * Flatten data
     *
     * @param array $data from UserEntityService->getTransportManagerApplications
     * @return array
     */
    public function map(array $data)
    {
        $newData = [];
        foreach ($data as $tmApplication) {
            $newData[] = [
                'transportManagerApplicationId' => $tmApplication['id'],
                'transportManagerApplicationStatus' => $tmApplication['tmApplicationStatus'],
                'licNo' => $tmApplication['application']['licence']['licNo'],
                'applicationId' => $tmApplication['application']['id'],
            ];
        };

        return $newData;
    }
}
