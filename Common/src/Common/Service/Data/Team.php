<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Dvsa\Olcs\Transfer\Query\Team\TeamList;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class Team
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Team extends AbstractDataService implements ListData
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
            $optionData[$datum['id']] = $datum['name'];
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

        return $this->formatData($data);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @internal param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Team'))) {

            $params = [
                'sort'  => 'name',
                'order' => 'ASC',
                'page'  => 1,
                'limit' => 100
            ];
            $this->setData('Team', false);
            $dtoData = TeamList::create($params);

            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $this->setData('Team', false);
            if (isset($response->getResult()['results'])) {
                $this->setData('Team', $response->getResult()['results']);
            }
        }

        return $this->getData('Team');
    }
}
