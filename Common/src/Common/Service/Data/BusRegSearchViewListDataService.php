<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Common\Service\Data\AbstractDataService;
use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewContextList;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use Zend\ServiceManager\FactoryInterface;

/**
 * BusRegSearchView List data service.
 * Populates filter drop down lists on bus reg registrations page.
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BusRegSearchViewListDataService extends AbstractDataService implements ListData
{
    /**
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = $this->fetchListData($context);

        if (!$data) {
            return [];
        }

        return $this->formatData($data);
    }

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
            $optionData[$datum] = $datum;
        }

        return $optionData;
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @internal param $context
     * @return array
     */
    public function fetchListData($context)
    {
        $cacheId = 'BusRegSearchView' . ucfirst($context);
        if (is_null($this->getData($cacheId))) {
            $dtoData = BusRegSearchViewContextList::create(
                [
                    'context' => $context,
                    'sort' => $context,
                    'order' => 'ASC'
                ]
            );

            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData($cacheId, false);
            if (isset($response->getResult()['results'])) {
                $this->setData($cacheId, $response->getResult()['results']);
            }
        }

        return $this->getData($cacheId);
    }
}
