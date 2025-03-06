<?php

declare(strict_types=1);

namespace Common\Service\Data;

use Common\Exception\DataServiceException;
use Dvsa\Olcs\Transfer\Query as TransferQry;

class MessagingSubject extends AbstractListDataService
{
    public const SORT_BY = 'description';

    public const SORT_ORDER = 'ASC';

    /**
     * Fetch list data
     * @throw DataServiceException
     */
    public function fetchListData($context = null): array
    {
        $data = (array)$this->getData('subjects');

        if ($data !== []) {
            return $this->filterComplianceItems($data);
        }

        $response = $this->handleQuery(
            TransferQry\Messaging\Subjects\All::create([
                'sort' => static::SORT_BY,
                'order' => static::SORT_ORDER,
            ])
        );

        if (!$response->isOk()) {
            throw new DataServiceException('unknown-error');
        }

        $result = $response->getResult();

        $this->setData('subjects', ($this->filterComplianceItems($result['results'])));

        return $this->getData('subjects');
    }

    /**
     * Filter out compliance items from the list
     */
    private function filterComplianceItems(array $data): array
    {
        return array_filter($data, function ($item) {
            return $item['category']['id'] !== 2;
        });
    }
}
