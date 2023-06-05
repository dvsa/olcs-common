<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Data\Object\Search\Aggregations\DateRange as DateRange;
use Common\Service\Table\Formatter\SearchPeopleName;
use Common\Service\Table\Formatter\SearchPeopleRecord;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class People extends InternalSearchAbstract
{
    public const FOUND_AS_HISTORICAL_TM = 'Historical TM';

    /**
     * @var string
     */
    protected $title = 'People';

    /**
     * @var string
     */
    protected $key = 'people';

    /**
     * @var string
     */
    protected $searchIndices = 'person';

    /**
     * Contains an array of the instantiated Date Ranges classes.
     *
     * @var array
     */
    protected $dateRanges = [];

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
                new Filter\FoundType(),
                new Filter\FoundBy(),
                new Filter\LicenceStatus(),
            ];
        }

        return $this->filters;
    }

    /**
     * Returns an array of date ranges for this index
     *
     * @return array
     */
    public function getDateRanges()
    {
        if (empty($this->dateRanges)) {
            $this->dateRanges = [
                new DateRange\DateOfBirthFromAndTo()
            ];
        }

        return $this->dateRanges;
    }

    public function getVariables()
    {
        return [
            'title' => $this->getTitle(),
            'action_route' => [
                'route' => 'create_transport_manager',
                'params' => ['action' => null]
            ]
        ];
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            'crud' => [
                'actions' => [
                    'add' => [
                        'label' => 'Create Transport Manager',
                        'class' => 'govuk-button',
                        'requireRows' => false
                    ],
                ],
            ],
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50]
                ]
            ]
        ];
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            ['title' => 'Found as', 'name' => 'foundAs'],
            [
                'title' => 'Record',
                'formatter' => SearchPeopleRecord::class
            ],
            [
                'title' => 'Name',
                'formatter' => SearchPeopleName::class
            ],
            [
                'title' => 'DOB',
                'name' => 'personBirthDate',
                'formatter' => function ($row) {

                    return empty($row['personBirthDate']) ?
                        'Not known' : date(\DATE_FORMAT, strtotime($row['personBirthDate']));
                }
            ],
            [
                'title' => 'Date added',
                'name' => 'dateAdded',
                'formatter' => function ($row) {

                    return empty($row['dateAdded']) ? 'NA' : date(\DATE_FORMAT, strtotime($row['dateAdded']));
                }
            ],
            [
                'title' => 'Date removed',
                'name' => 'dateRemoved',
                'formatter' => function ($row) {

                    return empty($row['dateRemoved']) ? 'NA' : date(\DATE_FORMAT, strtotime($row['dateRemoved']));
                }
            ],
            [
                'title' => 'Disq?',
                'name' => 'disqualified',
                'formatter' => function ($row) {
                    if ($row['foundAs'] === self::FOUND_AS_HISTORICAL_TM) {
                        return 'NA';
                    }
                    return $row['disqualified'];
                }
            ]
        ];
    }
}
