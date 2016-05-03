<?php

namespace Common\Service\Data;

use Common\Exception\DataServiceException;
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

        return $this->formatData($data, $context);
    }

    /**
     * Format data!
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data, $context)
    {
        $optionData = [];

        foreach ($data as $datum) {
            $optionData[$datum[$this->getKeyField($context)]] = $datum[$this->getValueField($context)];
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
        if ($this->getData($cacheId) === null) {
            $order = 'ASC';
            $dtoData = BusRegSearchViewContextList::create(
                [
                    'context' => $context,
                    'sort' => $this->getValueField($context),
                    'order' => $order
                ]
            );

            $response = $this->handleQuery($dtoData);

            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }

            $this->setData($cacheId, false);
            $result = $response->getResult();
            if (isset($result['results'])) {
                $this->setData($cacheId, $result['results']);
            }
        }

        return $this->getData($cacheId);
    }

    /**
     * Get the Value field to use in the drop downs based on context
     *
     * @param $context
     * @return string
     * @throws DataServiceException
     */
    private function getValueField($context)
    {
        switch($context) {
            case 'licence':
                return 'licNo';
            case 'organisation':
                return 'organisationName';
            case 'busRegStatus':
                return 'busRegStatusDesc';
            default:
                throw new DataServiceException('Invalid context value used in data service');
        }
    }

    /**
     * Get the Key field to use in the drop downs based on context
     *
     * @param $context
     * @return string
     * @throws DataServiceException
     */
    private function getKeyField($context)
    {
        switch($context) {
            case 'licence':
                return 'licId';
            case 'organisation':
                return 'organisationId';
            case 'busRegStatus':
                return 'busRegStatus';
            default:
                throw new DataServiceException('Invalid context key used in data service');
        }
    }
}
