<?php

namespace Common\Service\Data;

/**
 * Class RefData
 * @package Common\Service
 */
class RefData extends AbstractData implements ListDataInterface
{
    use ListDataTrait;

    protected $serviceName = 'RefData';

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
