<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;

/**
 * Class RefData
 * @package Common\Service
 */
class RefData extends AbstractData implements ListData
{
    use ListDataTrait;

    protected $serviceName = 'RefData';

    /**
     * This method retrieves the description for a chosen
     * ref data record by key.
     *
     * @param $key
     * @return array
     */
    public function getDescription($key)
    {
        if (is_null($this->getData($key))) {

            $data = $this->getRestClient()->get(sprintf('/%s', $key));

            $this->setData($key, $data['description']);
        }

        return $this->getData($key);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchListData($category)
    {
        if (is_null($this->getData($category))) {
            $data = $this->getRestClient()->get(sprintf('/%s', $category));
            $this->setData($category, $data);
        }

        return $this->getData($category);
    }
}
