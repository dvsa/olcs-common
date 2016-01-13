<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Dvsa\Olcs\Transfer\Query\LocalAuthority\LocalAuthorityList as LocalAuthorityQry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class LocalAuthority
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LocalAuthority extends AbstractDataService implements ListData
{
    /**
     * Format data!
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data)
    {
        $optionData = [];

        foreach ($data as $datum) {
            $optionData[$datum['id']] = $datum['description'];
        }

        return $optionData;
    }

    /**
     * Format for groups
     *
     * @param array $data
     * @return array
     */
    public function formatDataForGroups(array $data)
    {
        $optionData = [];

        foreach ($data as $datum) {
            $taId = $datum['txcName'];
            if (!isset($optionData[$taId])) {
                $optionData[$taId] = [
                    'label' => $datum['trafficArea']['name'],
                    'options' => []
                ];
            }
            $optionData[$taId]['options'][$datum['id']] = $datum['description'];
        }

        return $optionData;
    }

    /**
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($category, $useGroups = false)
    {
        $data = $this->fetchListData();

        if (!$data) {
            return [];
        }

        if ($useGroups) {
            return $this->formatDataForGroups($data);
        }

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('LocalAuthority'))) {
            $dtoData = LocalAuthorityQry::create(['limit' => 1000, 'page' => 1]);
            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $data = $response->getResult()['results'];
            $this->setData('LocalAuthority', $data);
        }
        return $this->getData('LocalAuthority');
    }
}
