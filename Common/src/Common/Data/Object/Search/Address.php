<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Service\Table\Formatter\SearchAddressComplaint;
use Common\Service\Table\Formatter\SearchAddressOperatorName;

/**
 * Class Address
 * @package Common\Data\Object\Search
 */
class Address extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Address';

    /**
     * @var string
     */
    protected $key = 'address';

    /**
     * @var string
     */
    protected $searchIndices = 'address';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {
            $this->filters = [
                new Filter\AddressType(),
                new Filter\AddressComplaint(),
                new Filter\AddressOpposition(),
                new Filter\LicenceStatus(),
                new Filter\ApplicationStatus(),
                new Filter\AddressConditionUndertaking(),
                new Filter\GoodsOrPsv(),
            ];
        }

        return $this->filters;
    }

    /**
     * Gets columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Licence / Application',
                'formatter' => 'LicenceApplication'
            ],
            [
                'title' => 'Operator name',
                'name' => 'orgName',
                'formatter' => SearchAddressOperatorName::class
            ],
            [
                'title' => 'Address',
                'formatter' => 'Address',
                'addressFields' => ['saonDesc', 'paonDesc', 'street', 'locality', 'town', 'postcode']
            ],
            [
                'title' => 'Complaint',
                'formatter' => SearchAddressComplaint::class
            ],
            [
                'title' => 'Opposition',
                'formatter' => SearchAddressComplaint::class
            ],
            ['title' => 'C/U', 'name' => 'conditions'],
            [
                'title' => 'Date added',
                'formatter' => 'Date',
                'name' => 'createdOn'
            ],
            [
                'title' => 'Date removed',
                'formatter' => 'Date',
                'name' => 'deletedDate'
            ],
        ];
    }
}
