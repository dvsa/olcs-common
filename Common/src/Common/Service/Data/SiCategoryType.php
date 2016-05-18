<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\Si\SiCategoryTypeListData;

/**
 * Class SiCategoryType
 */
class SiCategoryType extends AbstractDataService implements ListData
{
    /**
     * Format data
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
     * @param mixed $category
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
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('SiCategoryType'))) {

            $dtoData = SiCategoryTypeListData::create(
                [
                    'sort'  => 'description',
                    'order' => 'ASC',
                ]
            );

            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData('SiCategoryType', false);

            if (isset($response->getResult()['results'])) {
                $this->setData('SiCategoryType', $response->getResult()['results']);
            }
        }

        return $this->getData('SiCategoryType');
    }
}
