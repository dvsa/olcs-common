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
     *
     * @param array $context Parameters
     *
     * @return array
     * @throw DataServiceException
     */
    public function fetchListData($context = null)
    {
        $data = (array)$this->getData('subjects');

        if (count($data) !== 0) {
            return $data;
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

        $this->setData('subjects', ($result['results'] ?? NULL));

        return $this->getData('subjects');
    }
}
