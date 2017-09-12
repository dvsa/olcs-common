<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Data\Object\Search\Aggregations\DateRange as DateRange;

/**
 * Class People
 * @package Common\Data\Object\Search
 */
class People extends InternalSearchAbstract
{
    const FOUND_AS_HISTORICAL_TM = 'Historical TM';

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

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            'crud' => [
                'links' => [
                    'create-transport-manager' => [
                        'label' => 'Create Transport Manager',
                        'class' => 'primary js-modal-ajax',
                        'route' => [
                            'route' => 'create_transport_manager'
                        ]
                    ]
                ]
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
            ['title' => 'Found as', 'name'=> 'foundAs'],
            [
                'title' => 'Record',
                'formatter' => function ($row, $column, $serviceLocator) {
                    $urlHelper = $serviceLocator->get('Helper\Url');
                    if (!empty($row['applicationId']) && !empty($row['licNo'])) {
                        return sprintf(
                            '%s / <a href="%s">%s</a>',
                            $this->formatCellLicNo($row, $urlHelper),
                            $urlHelper->fromRoute('lva-application', ['application' => $row['applicationId']]),
                            $row['applicationId']
                        );
                    } elseif (!empty($row['tmId']) && $row['foundAs'] !== self::FOUND_AS_HISTORICAL_TM) {
                        $tmLink = sprintf(
                            '<a href="%s">TM %s</a>',
                            $urlHelper->fromRoute('transport-manager/details', ['transportManager' => $row['tmId']]),
                            $row['tmId']
                        );

                        if (!empty($row['licNo'])) {
                            $licenceLink = $this->formatCellLicNo($row, $urlHelper);
                            return $tmLink . ' / ' . $licenceLink;
                        }

                        return $tmLink;
                    } elseif (!empty($row['licTypeDesc']) && !empty($row['licStatusDesc'])) {
                        return sprintf(
                            '<a href="%s">%s</a>, %s<br />%s',
                            $urlHelper->fromRoute('licence', ['licence' => $row['licId']]),
                            $row['licNo'],
                            $row['licTypeDesc'],
                            $row['licStatusDesc']
                        );
                    } elseif (!empty($row['licNo'])) {
                        return $this->formatCellLicNo($row, $urlHelper);

                    } elseif (!empty($row['applicationId'])) {
                        return sprintf(
                            '<a href="%s">%s</a>, %s',
                            $urlHelper->fromRoute('lva-application', ['application' => $row['applicationId']]),
                            $row['applicationId'],
                            $row['appStatusDesc']
                        );
                    }
                    return '';
                }
            ],
            [
                'title' => 'Name',
                'formatter' => function ($row, $column, $serviceLocator) {
                    if ($row['foundAs'] === self::FOUND_AS_HISTORICAL_TM) {
                        $urlHelper = $serviceLocator->get('Helper\Url');
                        return sprintf(
                            '<a href="%s">%s</a>',
                            $urlHelper->fromRoute('historic-tm', ['historicId' => $row['tmId']]),
                            $row['personFullname']
                        );
                    } else {
                        return $row['personFullname'];
                    }
                }
            ],
            [
                'title' => 'DOB',
                'name'=> 'personBirthDate',
                'formatter' => function ($row) {

                    return empty($row['personBirthDate']) ?
                        'Not known' : date(\DATE_FORMAT, strtotime($row['personBirthDate']));
                }
            ],
            [
                'title' => 'Date added',
                'name'=> 'dateAdded',
                'formatter' => function ($row) {

                    return empty($row['dateAdded']) ? 'NA' : date(\DATE_FORMAT, strtotime($row['dateAdded']));
                }
            ],
            [
                'title' => 'Date removed',
                'name'=> 'dateRemoved',
                'formatter' => function ($row) {

                    return empty($row['dateRemoved']) ? 'NA' : date(\DATE_FORMAT, strtotime($row['dateRemoved']));
                }
            ],
            [
                'title' => 'Disq?',
                'name'=> 'disqualified',
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
