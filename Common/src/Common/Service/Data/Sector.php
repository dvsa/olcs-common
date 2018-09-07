<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\Permits\SectorsList;


/**
 * Class Sector
 *
 * @package Common\Service\Data
 */
class Sector extends AbstractDataService implements ListData
{
    /**
     * Format data
     *
     * @param array $data Data
     *
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
     * Fetch list options
     *
     * @param string $category Category
     * @param bool $useGroups Use groups
     *
     * @return array
     * @throws UnexpectedResponseException
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
     * Fetch list data
     *
     * @return array
     * @throws UnexpectedResponseException
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Sector'))) {

            $dtoData = SectorsList::create(array());
            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData('Sector', false);

            if (isset($response->getResult()['results'])) {
                $this->setData('Sector', $response->getResult()['results']);
            }
        }

        return $this->getData('Sector');
    }
}