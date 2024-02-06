<?php

namespace Common\Service\Data;

use Common\Exception\DataServiceException;
use Dvsa\Olcs\Transfer\Query as TransferQry;

class MessagingSubject extends AbstractListDataService
{
    protected static $sort = 'description';
    protected static $order = 'ASC';

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

        if (0 !== count($data)) {
            return $data;
        }

        $response = $this->handleQuery(
            TransferQry\Messaging\Subjects\All::create([
                'sort' => self::$sort,
                'order' => self::$order,
            ])
        );

        if (!$response->isOk()) {
            throw new DataServiceException('unknown-error');
        }

        $result = $response->getResult();

        $this->setData('subjects', (isset($result['results']) ? $result['results'] : null));

        return $this->getData('subjects');
    }
}
