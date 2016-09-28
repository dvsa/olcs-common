<?php

namespace Common\Service\Data;

use Common\Service\Data\AbstractDataService;
use Common\Service\Data\Interfaces\ListData;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Dvsa\Olcs\Transfer\Query\ContactDetail\ContactDetailsList;

/**
 * Class ContactDetails
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ContactDetails extends AbstractDataService implements ListData
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
     * @param string $category  Category
     * @param bool   $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($category, $useGroups = false)
    {
        $data = $this->fetchListData($category);

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
    }

    /**
     * Fetch list data
     *
     * @param string $category Category
     *
     * @return array
     * @throw UnexpectedResponseException
     */
    public function fetchListData($category)
    {
        if (is_null($this->getData('ContactDetails'))) {

            $params = [
                'sort'  => 'description',
                'order' => 'ASC',
                'page'  => 1,
                'limit' => 100,
                'contactType' => $category
            ];
            $dtoData = ContactDetailsList::create($params);
            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData('ContactDetails', false);

            if (isset($response->getResult()['results'])) {
                $this->setData('ContactDetails', $response->getResult()['results']);
            }
        }

        return $this->getData('ContactDetails');
    }
}
