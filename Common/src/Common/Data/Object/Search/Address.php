<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Util\Escape;

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
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => function ($data, $column, $serviceLocator) {
                    $urlHelper  = $serviceLocator->get('Helper\Url');

                    $licenceLink = sprintf(
                        '<a href="%s">%s</a>',
                        $urlHelper->fromRoute('licence', ['licence' => $data['licId']]),
                        $data['licNo']
                    );

                    if (isset($data['appId'])) {
                        $appLink = sprintf(
                            '<a href="%s">%s</a>',
                            $urlHelper->fromRoute('lva-application', ['application' => $data['appId']]),
                            $data['appId']
                        );

                        return $licenceLink . ' / ' . $appLink;
                    }

                    return $licenceLink;
                }
            ],
            ['title' => 'Licence status', 'name'=> 'licStatusDesc'],
            [
                'title' => 'Operator name',
                'name'=> 'orgName',
                'formatter' => function ($data, $column, $serviceLocator) {
                    $urlHelper  = $serviceLocator->get('Helper\Url');
                    return sprintf(
                        '<a href="%s">%s</a>',
                        $urlHelper->fromRoute('operator/business-details', ['organisation' => $data['orgId']]),
                        Escape::html($data['orgName'])
                    );
                }
            ],
            [
                'title' => 'Address',
                'formatter' => 'Address',
                'addressFields' => ['saonDesc', 'paonDesc', 'street', 'locality', 'town', 'postcode']
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
            ],
            [
                'title' => 'Date added',
                'formatter' => 'Date',
                'name'=> 'createdOn'
            ],
            [
                'title' => 'Date removed',
                'formatter' => 'Date',
                'name'=> 'deletedDate'
            ],
        ];
    }
}
