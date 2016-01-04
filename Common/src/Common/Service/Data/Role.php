<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Dvsa\Olcs\Transfer\Query\User\RoleList;
use Common\Service\Data\AbstractDataService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class Role
 * @package Common\Service
 */
class Role extends AbstractDataService implements ListData
{
    /**
     * @param $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $optionData = [];
        $data = $this->fetchListData();

        foreach ($data as $datum) {
            $optionData[$datum['role']] = $datum['description'];
        }

        return $optionData;
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @internal param $category
     * @return array
     */
    public function fetchListData()
    {
        if (is_null($this->getData('Role'))) {

            $this->setData('Role', false);
            $dtoData = RoleList::create([]);

            $response = $this->handleQuery($dtoData);
            if (!$response->isOk()) {
                throw new UnexpectedResponseException('unknown-error');
            }
            $this->setData('Role', false);
            if (isset($response->getResult()['results'])) {
                $this->setData('Role', $response->getResult()['results']);
            }
        }

        return $this->getData('Role');
    }
}
