<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

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
                    return '<a href="/licence/' . $data['licId'] . '">' . $data['licNo'] . '</a>';
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data) {
                    return '<a href="/operator/' . $data['orgId'] . '">' . $data['orgName'] . '</a>';
                }
            ],
            [
                'title' => 'Address',
                'formatter' => function ($row) {

                    $address = [

                        $row['street'],
                        $row['locality'],
                        $row['town'],
                        $row['postcode']
                    ];

                    return implode(', ', $address);
                }
            ],
            [
                'title' => 'Complaint',
                'formatter' => function ($row) {

                    if ($row['complaintCaseId']) {
                        return '<a href="/case/details/' . $row['complaintCaseId'] . '">Yes</a>';
                    }

                    return 'No';
                }
            ],
            [
                'title' => 'Opposition',
                'formatter' => function ($row) {

                    if ($row['oppositionCaseId']) {
                        return '<a href="/case/details/' . $row['oppositionCaseId'] . '">Yes</a>';
                    }

                    return 'No';
                }
            ]
        ];
    }
}
