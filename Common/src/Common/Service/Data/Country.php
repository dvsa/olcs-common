<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\AbstractDataService;
use Dvsa\Olcs\Transfer\Query\ContactDetail\CountryList;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class Country
 * @package Common\Service
 */
class Country extends AbstractDataService implements ListData
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
            $optionData[$datum['id']] = $datum['countryDesc'];
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

        if ('isMemberState' == $category) {
            $data = $this->removeNonMemberStates($data);
        }

        return $this->formatData($data);
    }

    public function removeNonMemberStates($data)
    {
        $members = [];

        foreach ($data as $state) {

            if (trim($state['isMemberState']) == 'Y') {

                $members[] = $state;
            }
        }

        return $members;
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Country'))) {
            $params = [
                'sort' => 'countryDesc',
                'order' => 'ASC'
            ];
            $dtoData = CountryList::create($params);

            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $this->setData('Country', false);
            if (isset($response->getResult()['results'])) {
                $this->setData('Country', $response->getResult()['results']);
            }
        }

        return $this->getData('Country');
    }
}
