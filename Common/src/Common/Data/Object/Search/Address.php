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
                'addressFields' => ['paonDesc', 'saonDesc', 'street', 'locality', 'town', 'postcode'],
                'formatter' => 'Address'
            ],
            [
                'title' => 'Complaint',
                'formatter' => function ($row, $column, $serviceLocator) {

                    if ($row['complaintCaseId']) {
                        $urlHelper  = $serviceLocator->get('Helper\Url');
                        return sprintf(
                            '<a href="%s">Yes</a>',
                            $urlHelper->fromRoute('licence/opposition', ['licence' => $row['licId']])
                        );
                    }

                    return 'No';
                }
            ],
            [
                'title' => 'Opposition',
                'formatter' => function ($row, $column, $serviceLocator) {

                    if ($row['oppositionCaseId']) {
                        $urlHelper  = $serviceLocator->get('Helper\Url');
                        return sprintf(
                            '<a href="%s">Yes</a>',
                            $urlHelper->fromRoute('licence/opposition', ['licence' => $row['licId']])
                        );
                    }

                    return 'No';
                }
            ]
        ];
    }
}
