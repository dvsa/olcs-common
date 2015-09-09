<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

/**
 * Class Address
 * @package Common\Data\Object\Search
 */
class OperatingCentreSelfserve extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Operating centres';

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
     * @return array
     */
    public function getSettings()
    {
        return [
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50, 100]
                ]
            ],
            'layout' => 'headless'
        ];
    }

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {

            $this->filters = [
                new Filter\LicenceType()
            ];
        }

        return $this->filters;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => function ($data) {
                    return '<a href="/view-details/licence/7' . $data['licId'] . '">' . $data['licNo'] . '</a>/'
                    . $data['appId'] . '<br />' . $data['licStatus'];
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName'
            ],
            [
                'title' => 'Address',
                'formatter' => function ($row) {

                    $address = [

                        $row['street'],
                        $row['locality'],
                        '<br />' . $row['town'],
                        $row['postcode']
                    ];

                    return implode(', ', $address);
                }
            ],
        ];
    }
}
